## About Simple image resizer

Simple image resizer is a demonstrative api for image uploading and image resizing.

### Use

#####Run tests:
- docker-compose -f docker-compose.testing.yml up --no-deps --build test

#####Run app:

-  docker-compose up --no-deps --build api


### API

####Store image:

POST: /api/image
######Parameters:
mame: required|string  
file: required|Binary file

Notes: 
- Set header "Content-Type" to "multipart/form-data"
- name must be unique
- File must be a jpg,jpeg,png or gif

####Delete Image:
DELETE: /api/image/{imageId}

Notes: 
- imageId must be an id of an image

####List images:
GET: /api/image
######Parameters:
name: optional|string  
data: optional|it should be 0 or 1, for example /api/image?data=1

Notes:
- if name is set it search as a like %name% in the database
- if data is 1 the api returns even the data of the images encoded in base64

####Resize images:
PUT: /api/resize
######Parameters:
name: required|string  
width: required|int  
height: required|int  

Notes:

- It resize the image of the image at the width and height size

#General Notes:
- All the tests are located under the tests/Feature directory
- In the docs directory is located a configuration file that can be imported on Insomnia api client, the famous api tester.
- For data I used a sqlite database.
- the docker images generated and loaded by Dockerfile and docker-compose files are not suited for production but only for testing.
- It should be run on localhost.
- For users that don't know laravel, controllers are located in app/Http/Controllers directory. Model is located in app/Models.
- As editor to navigate through the classes I suggest PhpStorm.

## License

The simple-image-resizer is a open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
