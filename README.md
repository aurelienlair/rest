# rest
Rest API usage

In order to have a pretty Json parser I recommend to install this before starting (these are instructions for Ubuntu).

Curl is necessary to test the APIs
```
aurelien@linux:~$ sudo apt-get install curl
```

I am using Python 2.7 but of course feel free to use your favourite version.

```
aurelien@linux:~$ sudo apt-get update
aurelien@linux:~$ sudo apt dist-upgrade
aurelien@linux:~$ sudo apt-get install python2.7
aurelien@linux:~$ sudo apt-get install python-pip
aurelien@linux:~$ pip -V 
pip 9.0.1

aurelien@linux:~$ pip search mjson.tool
mjson (0.3.1)  - Extended "python -mjson.tool"
aurelien@linux:~$ pip install mjson.tool
```
Now that we have a pretty Json parser let's get started!
