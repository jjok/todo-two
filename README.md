
Build

    docker build -t todotwo .

Run (TODO)

    docker run --rm -it todotwo 
    
Test

    docker run --rm -it -v $HOME/Workspace/todoTwo:/tmp todotwo vendor/bin/phpunit

TODO
----

* [X] Create task
* [X] Complete task
* [X] Change task name
* [X] Change task priority
* [ ] Add Priority value object
* [ ] Serialise events
* [ ] Store events
* [ ] Projection of all events with priority
* [ ] HTTP frontend
* [ ] Set up web server
* [ ] Client
* [ ] Migrate data from old app
