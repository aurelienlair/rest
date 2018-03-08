<?php

namespace Cast;

class CsvParser
{
    private $terminator = "\n";
    private $separator = ",";
    private $enclosed = '"';
    private $escaped = "\\"; 
    private $mimeType = "text/csv";
    private $fileName;
    private $headerColumns = [];
    private $rows = [];

    public function __construct($fileName='foo')
    {
        $this->fileName = $fileName;
    }

    public function headerColumns($columns=false)
    {
        if ($columns) {
            if ($this->colCheckMismatch($columns)) {
                echo 'Unable to add header columns - row column mismatch';
            } else {
                if (is_array($columns)) {
                    foreach ($columns as $column) {
                        $this->headerColumns[] = $column;
                    }
                } else {
                    $this->headerColumns[0] = $columns;
                }
            }
        } else {
            return $this->headerColumns;
        }
    }

    public function addRow($row)
    {
        if ($this->colCheckMismatch($row)) {
            echo 'Unable to insert row into CSV - header column mismatch';
        } else {
            if (is_array($row)) {
                $this->rows[] = $row;
            } else {
                $this->rows[][0] = $row;
            }
        }
    }

    public function removeRow($rowNumber)
    {
        $rows = $this->rows;
        unset($rows[$rowNumber]);
        $this->rows = array_values($rows);
    }

    public function updateRow($rowNumber, $row)
    {
        $this->rows[$rowNumber] = $row;
    }

    public function save()
    {
        $schema_insert = '';
        $out = '';

        if (!$handler = fopen($this->fileName, 'w')) {
            throw new \Exception("Cannot write to file ({$fileName})");
        }

        if ($this->headerColumns) {
            foreach ($this->headerColumns as $column_number => $column) {
                $l = $this->enclosed 
                    . str_replace($this->enclosed, $this->escaped . $this->enclosed, stripslashes($column)) 
                    . $this->enclosed;
                $schema_insert .= $l;
                $schema_insert .= $this->separator;
            }

            fwrite($handler, trim(substr($schema_insert, 0, -1)) . $this->terminator);
        }

        if ($this->rows) {
            foreach ($this->rows as $row) {
                $str = '';
                foreach ($row as $column => $value) {
                    $schema_insert = '';
                    if (isset($value)) {
                        if ($this->enclosed == '') {
                            $schema_insert .= $value;
                        } else {
                            $schema_insert .= $this->enclosed 
                                .  str_replace($this->enclosed, $this->escaped . $this->enclosed, $value) 
                                .  $this->enclosed;
                        }
                    } else {
                        $schema_insert .= '';
                    }

                    if ($column < count($row) - 1) {
                        $schema_insert .= $this->separator;
                    }

                    $str .= $schema_insert;
                }
                
                fwrite($handler, $str . $this->terminator);
            }
        }

        fclose($handler);
    }

    public function readCSV($headers=false)
    {
        $row = 0;

        if (!file_exists($this->fileName)) {
            touch($this->fileName);
        }

        if (($handle = fopen($this->fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, $this->separator, $this->enclosed, $this->escaped)) !== FALSE) {
                $num = count($data);

                if ($row == 0) {
                    $first_row_columns = $num;

                    if ($headers) {
                        $headerRow = [];
                        for ($c=0; $c < $num; $c++) {
                            $headerRow[$c] = $data[$c];
                        }
                        $this->headerColumns($headerRow);
                    } else {
                        for ($c=0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }
                } else {
                    if ($num != $first_row_columns) {
                        echo 'The number of columns in row ' 
                            . $row 
                            . ' does not match the number of columns in row 0';
                        fclose($handle);
                        return false;
                    }

                    if ($headers) {
                        for ($c=0; $c < $num; $c++) {
                            $this->rows[$row-1][$c] = $data[$c];
                        }
                    } else {
                        for ($c=0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }
                }

                $row++;
            }

            fclose($handle);
        }
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function totalRows()
    {
        return count($this->rows);
    }

    public function totalCols()
    {
        if ($this->headerColumns) {
            return count($this->headerColumns);
        } elseif (!$this->headerColumns && $this->rows) {
            return count($this->rows[0]);
        }

        return 0;
    }

    private function colCheckMismatch($row)
    {
        if ($this->headerColumns) {
            if (count($this->headerColumns) != count($row)) {
                return true;
            }
        } elseif (!$this->headerColumns && $this->rows) {
            if (count($this->rows[0]) != count($row)) {
                return true;
            }
        }

        return false;
    }
}
