VagrantBundle
=============

Symfony2 bundle to generate a working Vagrant environment


Usage
-----

Run the command interactively and answer the prompts:

    $ php app/console

    Welcome to the Symfony2 Vagrant generator
    ...


Or provide the options at once:

    $ php app/console generate:vagrant  \
        --no-interaction                \
        --host=vagrant                  \
        --ip=10.33.33.33                \
        --url=http://files.vagrantup.com/lucid32.box

A `Vagrantfile` will be generated in your *current working directory*.


Authors
-------

- [Eric Clemmons][1]
- [Paul Seiffert][2]


License
-------

MIT


[1]: https://github.com/ericclemmons
[2]: https://github.com/seiffert
