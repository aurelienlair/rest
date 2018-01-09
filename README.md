# rest
Rest API usage

REST (REpresentational State Transfer) is an architectural style for developing web services.
The (main) characteristics are that REST is:
- stateless
- client-server
- cacheable
- interface/uniform contract
- use HTTP methods explicitly (following what defined by RFC 2616)
The principle is that the APIs are resources based.
REST use HTTP the way it's meant to be.

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
aurelien@linux:~$ /usr/bin/pip -V 
pip 9.0.1

aurelien@linux:~$ /usr/bin/pip search mjson.tool
mjson (0.3.1)  - Extended "python -mjson.tool"
aurelien@linux:~$ /usr/bin/pip install mjson.tool
```
Now that we have a pretty Json parser let's get started!

Please note we are assuming you have a PHP client on your machine.

We are assuming you have a PHP client on your machine.

Before starting please configure your host file as below:

`aurelien@linux:~$ echo "127.0.0.1 www.restisthebest.com" >> /etc/hosts`

Then launch the server

`aurelien@linux:~$ /usr/bin/php -S www.restthebest.com:8888 public/index.php public/index.php`

In order to check if it works try this:

```
aurelien@linux:~$ curl -v "http://www.restisthebest.com:8888"

> GET / HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: */*
>
< HTTP/1.1 200 OK
< Host: www.restisthebest.com:8888
< Date: Tue, 09 Jan 2018 16:12:13 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: text/html; charset=utf-8
<

Hello world
```

Note: the following introduction of REST will use a domain with `actors`. It will show how with simple APIs we are able to CRUD by following the http standards.

1. POST

As specified within the [RFC](https://tools.ietf.org/html/rfc7231#section-4.3.3) the `POST` method is used to request that the origin server accept the entity enclosed in the request as a new subordinate of the resource identified by the Request-URI in the Request-Line.


| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|POST   |Create   |201 (Created), 'Location' header with link to /actor/{id} containing new ID.   | 404 (Not Found), 409 (Conflict) if resource already exists..  |


