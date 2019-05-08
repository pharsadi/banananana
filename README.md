# Banananana

Banananana is a set of API for Bob to track his banana

## Requirements - Dev Environment

* Composer - https://getcomposer.org
* VirtualBox - https://www.virtualbox.org
* Vagrant - https://www.vagrantup.com
* Homestead - https://laravel.com/docs/5.8/homestead

## Installation - Dev Environment

### Install VirtualBox

https://www.virtualbox.org/wiki/Downloads

### Install Vagrant

https://www.vagrantup.com/downloads.html

### Install Homestead Vagrant Box

Run the following command in terminal. This may take some time to run.


```
vagrant box add laravel/homestead
```


## Configuration - Dev Environment

### Initial Setup

Install Homestead using Composer

```
composer require laravel/homestead --dev
```

Install dependencies via Composer

```
composer up
```

Generate `Vagrantfile` and `Homestead.yaml`

```
php vendor/bin/homestead make
```

Add an entry in `/etc/hosts` for `homestead.test`

```
192.168.10.10 homestead.test
```

Next is turn on vagrant

```
vagrant up
```

Now, the project can be accessed at `http://homestead.test`.

More details can be found on https://laravel.com/docs/5.8/homestead#per-project-installation


## Unit Tests

SSH to the vagrant box

```
vagrant ssh
```

Go to the `~/code` directory and run the following command

```
./vendor/bin/phpunit tests/Feature/
```

### Migrate & Seed Database

This is required to set up database and to seed it with initial data.

SSH to the vagrant box

```
vagrant ssh
```

Go to the `~/code` directory and run the following command

```
php artisan migrate:refresh --seed
```


## API Specification

Please make sure to run the migrate & database seed before calling the APIs.

### Sell API

#### Method: POST

#### Path: /api/sell/{item}

#### Path Params:

##### {item}

* Required: Yes
* Type: String
* Example: banana
* Notes: "banana" is built in. More items can be added to the `items` table if needed.

#### Body Params:

##### {quantity}

* Required: Yes
* Type: Integer
* Example: 4
* Notes: Quantity of item in the transaction.

##### {transaction_date}

* Required: Yes
* Type: Date
* Example: 2019-05-01
* Notes: Transaction date. Must be in format YYYY-MM-DD.

##### {price}

* Required: No
* Type: Numeric
* Example: 0.05
* Notes: Item sell price. Default is $0.35.

#### Examples

**Request**
```
curl -X POST \
  http://homestead.test/api/sell/banana \
  -d '{
	"quantity" : 3,
	"transaction_date" : "2019-05-02"
}'
```
**Response**
```
{
    "success": true
}
```

**Request**
```
curl -X POST \
  http://homestead.test/api/sell/banana \
   -d '{
       	"quantity" : 50,
       	"transaction_date" : "2019-05-02"
}'
```
**Response**
```
{
    "success": false,
    "errors": {
        "quantity": [
            "Requested quantity is more than inventory"
        ]
    }
}
```

### Purchase API

#### Method: POST

#### Path: /api/purchase/{item}

#### Path Params:

##### {item}

* Required: Yes
* Type: String
* Example: banana
* Notes: "banana" is built in. More items can be added to the `items` table if needed.

#### Body Params:

##### {quantity}

* Required: Yes
* Type: Integer
* Example: 4
* Notes: Quantity of item in the transaction.

##### {transaction_date}

* Required: Yes
* Type: Date
* Example: 2019-05-01
* Notes: Transaction date. Must be in format YYYY-MM-DD.

##### {price}

* Required: No
* Type: Numeric
* Example: 0.05
* Notes: Item purchase price. Default is $0.20.


#### Examples

**Request**
```
curl -X POST \
  http://homestead.test/api/purchase/banana \
  -d '{
	"quantity" : 5,
	"transaction_date" : "2019-05-02"
}'
```
**Response**
```
{
    "success": true
}
```

**Request**
```
curl -X POST \
  http://homestead.test/api/purchase/banana \
   -d '{
	"quantity" : "A",
	"transaction_date" : "201902"
}'
```
**Response**
```
{
    "success": false,
    "errors": {
        "quantity": [
            "The quantity must be an integer."
        ],
        "transaction_date": [
            "The transaction date does not match the format Y-m-d."
        ]
    }
}
```

### Metrics API


#### Method: POST

#### Path: /api/metrics/{item}?start_date={startDate}&end_date={endDate}

#### Path Params:

##### {item}

* Required: Yes
* Type: String
* Example: banana
* Notes: "banana" is built in. More items can be added to the `items` table if needed.

##### {startDate}

* Required: Yes
* Type: Date
* Example: 2019-05-01
* Notes: Metrics start date. Must be in format YYYY-MM-DD.

##### {endDate}

* Required: Yes
* Type: Date
* Example: 2019-05-02
* Notes: Metrics end date. Must be in format YYYY-MM-DD.

#### Examples

**Request**
```
curl -X GET \
  'http://homestead.test/api/metrics/banana?start_date=2019-05-01&end_date=2019-05-05'
```
**Response**
```
{
    "new_inventory": 3,
    "expired_inventory": 0,
    "sold_count": 2,
    "profit": -0.30
}
```
**Request**
```
curl -X GET \
  'http://homestead.banananana/api/metrics/banana?start_date=20190501'
```
**Response**
```
{
    "success": false,
    "errors": {
        "start_date": [
            "The start date does not match the format Y-m-d."
        ],
        "end_date": [
            "The end date field is required."
        ]
    }
}
```

## HTTP Response Codes

* 201 - Created
* 200 - OK
* 400 - Bad Request
