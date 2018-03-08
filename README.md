# Rest what is this?

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
Alternatively to `mjson` you might want to use [jq](https://stedolan.github.io/jq/download/) which is really cool and easy to install...!

Now that we have a pretty Json parser let's get started!

Please note we are assuming you have a PHP client on your machine.

We are assuming you have a PHP client on your machine.

Before starting please configure your host file as below:

`aurelien@linux:~$ echo "127.0.0.1 www.restisthebest.com" >> /etc/hosts`

Then launch the server

`aurelien@linux:~$ /usr/bin/php -S www.restisthebest.com:8888 public/index.php`

In order to check if it works try this:

```
aurelien@linux:~$ /usr/bin/curl -v "http://www.restisthebest.com:8888/api/ping" \
 -H "Accept: application/json"

> GET /api/ping HTTP/1.1
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

pong
```

Note: the following introduction of REST will use a domain with `actors`. It will show how with simple APIs we are able to use CRUD operations by following the http standards.

1. POST

As specified within the [RFC](https://tools.ietf.org/html/rfc7231#section-4.3.3) the `POST` method is used to request that the origin server accept the entity enclosed in the request as a new subordinate of the resource identified by the Request-URI in the Request-Line.

So in our case with a `POST` request on the `actors` URI we will request the server to create a new resource within the `actors` collection.

The characteristics of a `POST` request are:

* it *not* [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will *not* produce the same result. 
* it is *not* safe, meaning they are only intended for creating new resources (while GET/HEAD HTTP methods do not modify resources)
* is *not* cacheable


| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|POST   |Create   |201 (Created), 'Location' header with link to /actors/{id} containing new ID.   | 404 (Not Found), 409 (Conflict) if the resource already exists..  |


The command to create a new actor in our small server is
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/x-www-form-urlencoded" \
-d "firstname=Ewan&lastname=McGregor&country=GB" \
-X POST "http://www.restisthebest.com:8888/api/actors" 

> POST /api/actors HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: */*
> Content-type: application/x-www-form-urlencoded
> Content-Length: 43
> 
< HTTP/1.1 201 Created
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 08:00:28 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< Location: http://www.restisthebest.com:8888/api/actors/fd015f0e682c4ec6e196ada96c7cdf3a
< 

aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-d '{"firstname":"Ewan","lastname":"McGregor","country":"GB"}' \
-X POST "http://www.restisthebest.com:8888/api/actors"

> POST /api/actors HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: */*
> Content-type: application/json
> Content-Length: 43
> 
< HTTP/1.1 201 Created
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 08:00:28 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< Location: http://www.restisthebest.com:8888/api/actors/fd015f0e682c4ec6e196ada96c7cdf3a
< 
```
You can see 2 different media types of the same request, one with the data in [Json](https://en.wikipedia.org/wiki/JSON) format, the other one is the [application/x-www-form-urlencoded](https://en.wikipedia.org/wiki/Percent-encoding#The_application.2Fx-www-form-urlencoded_type) media type.

2. GET

The GET method means retrieve whatever information (in the form of an entity) is identified by the Request-URI. If the Request-URI refers to a data-producing process, it is the produced data which shall be returned as the entity in the response and not the source text of the process, unless that text happens to be the output of the process.
It is considered as a safe method because it does not modify resources.

So in our case with a `GET` request on the `actor/{id}` URI we will request the server to get the resource identified by `{id}`.

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|GET   |Read   |200 (OK), lists the collection of actors.   | 200 (OK) single actor with the data in the body on the format requested on the header "Accept", 404 (Not Found) if the requested actor is not found |

The GET as a safe method can be cached, prefetched without any repercussions to the resource.

To get all the actors the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Accept: application/json" \
-X GET "http://www.restisthebest.com:8888/api/actors"

> GET /api/actors/ HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: application/json
> 
< HTTP/1.1 200 OK
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 08:12:44 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< 
[{"id":"fa15b3333349efc032cc8aada81a0e9c","firstname":"Robert","lastname":"De Niro","country":"US"},{"id":"3a760d3b7974cab159860f7660d90688","firstname":"Ewan","lastname":"McGregor","country":"GB"}]
```

To get all the actor which id is `fd015f0e682c4ec6e196ada96c7cdf3a` the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Accept: application/json" \
-X GET "http://www.restisthebest.com:8888/api/actors/fd015f0e682c4ec6e196ada96c7cdf3a" 

> GET /api/actors/fd015f0e682c4ec6e196ada96c7cdf3a HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: application/json
> 
< HTTP/1.1 200 OK
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 08:03:59 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< 
{"id":"54262a792a204cb1d6ce0770e5bb85c3","firstname":"Ewan","lastname":"MacGregor","country":"GB"}
```

When the requested resource (here `UNKNOWN_ID`) is not found the server must respond with a 404
```
/usr/bin/curl -v -H "Accept: application/json" \
-X GET "http://www.restisthebest.com:8888/api/actors/UNKNOWN_ID" 

> GET /api/actors/UNKNOWN_ID HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: application/json
> 
< HTTP/1.1 404 Not Found
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 08:25:24 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< 
```

The characteristics of a `GET` request are:

* it [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will produce the same result. 
* it is safe, meaning they are *not* intended for creating/modifying resources
* it is cacheable

In the above request we're requesting the server to repond with an `application/json` content (which is generally the one used on the server to server APIs)
3. HEAD 

Similary to the GET method, HEAD is used to retrieve a resource but must instead *not* return a message-body in the response. This method is often used for testing hypertext links for validity, accessibility, and recent modification.
It allows resources to be inspected without retrieving a representation (which is typically something that can do a client like a browser).

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|HEAD |Read   |200 (OK), lists the collection of actors   | 404 (Not Found) if the requested actor is not found |

To get all the actors the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-X HEAD "http://www.restisthebest.com:8888/api/actors" 

> HEAD /api/actors HTTP/1.1
> User-Agent: curl/7.35.0
> Host: www.restisthebest.com:8888
> Accept: */*
> Content-type: application/json
> 
< HTTP/1.1 200 OK
< Host: www.restisthebest.com:8888
< Date: Thu, 08 Mar 2018 09:39:15 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json
< 
```

To get all the actor which id is `1` the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-X HEAD "http://www.restisthebest.com:8888/api/actors/1" 
```

The characteristics of a `HEAD` request are:

* it [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will produce the same result. 
* it is safe, meaning they are *not* intended for creating/modifying resources
* it is cacheable

4. PUT 

This http method is used to create a resource, or overwrite it.

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|PUT   |Update/Create| 405 (Method Not Allowed) since the PUT is generally only used for a single resource| 200 (OK) or 204 (No Content) with the entire resource within the body. 404 (Not Found), if ID not found or invalid. |

To overwrite the properties of the actor which id is `1` the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-d '{"firstname":"James","lastname":"McGregor","country":"GB"}' \
-X PUT "http://www.restisthebest.com:8888/api/actors/1"
```

The same update using the form-encoded media type:
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-d '{"firstname":"James","lastname":"McGregor","country":"GB"}' \
-X POST "http://www.restisthebest.com:8888/api/actors/1" 
```

This method is considered indempotent because it can be called many times without different outcomes while as matter of fact it is *not* safe.

The characteristics of a `PUT` request are:

* it is [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will produce the same result. 
* it is *not* safe, meaning they are only intended for creating new resources (while GET/HEAD HTTP methods do not modify resources)

5. PATCH

This http method is used to update partially a resource. This method can be useful if you need to update a resource which contains a lot of data and you just need to update some of them.
The difference with the PUT and PATCH is the PUT creates a new resource from an old one while a PATCH is just replaces some parts of an existing resource.

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|PUT   |Update| 405 (Method Not Allowed) since the PUT is generally used for a single resource| 200 (OK) or 204 (No Content) with the modified parts of the resource within the body. 404 (Not Found), if ID not found or invalid. |

To partially update the properties of the actor which id is `1` the command is 
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-d '{"firstname":"James"}' \
-X PATCH "http://www.restisthebest.com:8888/api/actors/1"
```

The characteristics of a `PATCH` request are:

* it *not* [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will *not* produce the same result. 
* it is *not* safe, meaning they are only intended for updating partially resources which thefore can change (while GET/HEAD HTTP methods do not modify resources)

6. DELETE 

The DELETE method requests that the origin server deletes the resource identified by the Request-URI.
It basically deletes an actor resource on our case.

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|DELETE |Read   |200 (OK)   | 404 (Not Found)  |


Will send a command asking to the server to DELETE the resource identified by id 1:
```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-X DELETE "http://www.restisthebest.com:8888/api/actors/1"

* Connected to localhost (127.0.0.1) port 8888 (#0)
> DELETE /api/actors/1 HTTP/1.1
> User-Agent: curl/7.35.0
> Host: localhost:8888
> Accept: */*
> Content-type: application/json
> 
< HTTP/1.1 200 OK
< Host: localhost:8888
< Date: Mon, 26 Feb 2018 16:02:35 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json; charset=utf-8
< 
```

The characteristics of a `DELETE` request are:

* it is [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will produce the same result. 
* it is *not* safe, meaning they are only intended for deleting resources (while GET/HEAD HTTP methods do not modify resources)

7. OPTIONS 

The OPTIONS method represents a request for information about the communication options available on the request/response chain identified by the Request-URI. This method allows the client to determine the options and/or requirements associated with a resource, or the capabilities of a server, without implying a resource action or initiating a resource retrieval.

| HTTP Verb | CRUD  |  Entire Collection (e.g. /actors) | Specific Item (e.g. /actors/{id}  |
|---|---|---|---|
|OPTIONS |Read   |200 (OK), with a list of the allowed https methods   | 404 (Not Found)  |

```
aurelien@linux:~$ /usr/bin/curl -v -H "Content-type: application/json" \
-X OPTIONS "http://www.restisthebest.com:8888/api/actors" 

* Connected to localhost (127.0.0.1) port 8888 (#0)
> OPTIONS /api/actors HTTP/1.1
> User-Agent: curl/7.35.0
> Host: localhost:8888
> Accept: */*
> Content-type: application/json
> 
< HTTP/1.1 200 OK
< Host: localhost:8888
< Allow: GET,HEAD,POST,OPTIONS,DELETE
< Date: Mon, 26 Feb 2018 16:02:35 +0000
< Connection: close
< X-Powered-By: PHP/7.1.11-1+ubuntu14.04.1+deb.sury.org+1
< Content-type: application/json; charset=utf-8
< 
```

The characteristics of an `OPTIONS` request are:

* it [idempotent](https://en.wikipedia.org/wiki/Idempotence) which means that we can make that same call repeatedly it will produce the same result. 
* it is safe, meaning they are *not* intended for creating/modifying resources
* it is cacheable

### Laravel Rest API
If you want check [this](https://github.com/aurelienlair/rest-laravel) Laravel API rest API I implemented.
