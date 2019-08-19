
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
* [X] Add Priority value object
* [X] Serialise events
* [X] Store events
* [ ] Projection of all tasks with priority
* [ ] HTTP frontend
* [ ] Set up web server
* [ ] Client
* [ ] Migrate data from old app
