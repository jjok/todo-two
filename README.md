
Build

    docker build -t todotwo .

Run (TODO)

    docker run --rm -it todotwo 
    
Test

    docker run --rm -it -v $HOME/Workspace/todoTwo:/tmp todotwo vendor/bin/phpunit

    docker run --rm -it -v $HOME/Workspace/todoTwo:/tmp todotwo php composer.phar install

TODO
----

* [X] Create task
* [X] Complete task
* [X] Change task name
* [X] Change task priority
* [X] Add Priority value object
* [X] Serialise events
* [X] Store events
* [X] Rebuild Task aggregate from event stream
* [X] Projection of all tasks with priority
* [X] Save projection to file
* [X] Rebuild projections when event is stored
* [ ] Require task name to be unique
* [ ] HTTP frontend
* [ ] Set up web server
* [ ] Client
* [ ] Migrate data from old app
* [ ] Archive tasks
