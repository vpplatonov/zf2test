<?php
return array(

        'doctrine' => array(
                'eventmanager' => array(
                        'orm_default' => array(
                                'subscribers' => array(
                                        // pick any listeners you need
                                        'Gedmo\Tree\TreeListener',
                                        'Gedmo\Timestampable\TimestampableListener',
                                        //                'Gedmo\Sluggable\SluggableListener',
                                        'Gedmo\Translatable\TranslatableListener',
                                        //                'Gedmo\Sortable\SortableListener'
                                ),
                        ),
                ),

                'driver' => array(
                        'twitter_entity' => array(
                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                'paths' => array(__DIR__ . '/../src/Application/Entity'),
                        ),
                        'translatable_metadata_driver' => array(
                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                'cache' => 'array',
                                'paths' => array(
                                        'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
                                ),
                        ),
                        'orm_default' => array(
                                'drivers' => array(
                                        'Application\Entity'  => 'twitter_entity',
                                        'Gedmo\Translatable\Entity' => 'translatable_metadata_driver',
                                ),
                        ),
                ),
        ),
);
