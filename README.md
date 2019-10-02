
Build

    docker build -t todotwo .

Run (TODO)

    docker run --rm -it todotwo 
    
Test

    docker run --rm -it -v $HOME/Workspace/todoTwo:/tmp todotwo php composer.phar install

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
* [X] Rebuild Task aggregate from event stream
* [X] Projection of all tasks with priority
* [X] Save projection to file
* [X] Rebuild projections when event is stored
* [ ] Add actual High, Medium, Low priority to All Tasks projection.
* [ ] Make users a thing. Have an ID to complete tasks with.
* [ ] Require task ID to be unique
* [ ] Require task name to be unique
* [ ] HTTP frontend
* [ ] Home Assistant component
* [ ] Hass.io add-on
* [ ] Migrate data from old app
* [ ] Home Assistant UI
* [ ] Client? UI to create and edit tasks
* [ ] Allow tasks to be archived when no longer required
