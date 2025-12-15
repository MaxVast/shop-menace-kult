<?php

namespace App\Entity\Media;

interface MediaObjectInterface
{
    public function getRelation();

    public function setRelation(object $object);

    public static function getRelationClassname(): string;

    /**
     * Return the folder's name for upload.
     */
    public static function getRelationName(): string;

    /**
     * Return the file's name for upload.
     */
    public function getModelName(): string;
}
