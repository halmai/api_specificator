# API Specificator

API Specificator is a command line tool written in PHP to preprocess swagger.yaml files. 

It reads your API specification from an input file and writes the output to the standard output. 
Future version will be able to send output to an arbitrary file. Sorry for the temporal inconvenience. :)

## Installation ##

1. Install php on your computer
1. Clone or download the files from this repo into your local environment
1. Modify the provided `test/api_spec.as` or build your own one according to your needs
1. run the program with the `php api_specificator.php <input filename>` command (see --help for more)
1. copy-paste the created output into your swagger editor
1. have fun with it.

## Goal ##

The goal of this program is to ease specifying of APIs. 
Instead of typing a lot in swagger, you can write your specification in a less flexible but more dense way.

## What does it add? ##

The file format `API Specificator` can read is a superset of the valid `swagger.yaml` files with some differences. 
That is, you can start from your existing `swagger.yaml` file and change it according to your needs.

* The ap_spec.as file doesn't have to be a valid yaml file
* You can indent by TABs or SPACEs, the result will be converted to SAPCE-indentation (as per swagger requirements)
* Sections starting with a `request_template:` line are converted into `schema:` sections 

## What can be in a request_template: section? ##

Instead of writing a long and verbose description with a lot of repetition in swagger, you can specify the request schema in a much more concise way. 

Instead of the swagger-way of a schema specification like this:

```
        schema:
            type: object
            properties:
                age:
                    type: number
                    description:  asdasdasd
                iq:
                    type: integer
                is_male:
                    type: boolean
                email:
                    type: string
                    description:  email address of the user
                password:
                    type: string
                displayed_name:
                    type: string
            example:
                age: 43.5
                iq: 100
                is_male: true
                email: "johndoe@gmail.com"
                password: "abc123"
                displayed_name: "Johny B. Good"
            required:
                - email
                - password
                - displayed_name
```
 you can write just what you really need to:
 
 ```
		request_template:
			{
				age: 43.5 // asdasdasd
				iq: 100
				is_male: true
				*email:  "johndoe@gmail.com"  // email address of the user
				*password: "abc123"
				*displayed_name: "Johny B. Good"
			}
```

That is, you just list the fileds in each line, prepend them with `*` if they are required, give an example value for each of them and a potentional one-liner description after the `//` symbol.

From these lines the `API Specificator` will determine the name, data type, example value, requiredness and the description of each field. 

The interpretation of the example values to determine the data type works according to the following rules:
- if the value is surrounded by `"` symbols then it is a `string`
- if the value equals `true` or `false` (without `"` symbols) then it is a `boolean`
- if it contains only digits then it is an `integer`
- if it contains digits and at least one of the `.+_eE` characters then it is a (floating point) `number` 
- otherwise it is a string

## TODO ##

This version of the program is just a very basic one. It was made just one evening so don't be too harsh and don't expect too much. :) 
There are a lot of things to work on:

- produce output file instead of stdout
- work with multi-line descriptions
- adding ability to describe API responses as well, not just requests
- working with arrays
- macro handling
- etc.

