<?php

namespace App\Listener;

use App\Entity\Property;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::preUpdate,
            Events::preRemove
        );
    }

    public function preUpdate(LifecycleEventArgs   $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Property) {
            return;
        }

        else if ($entity instanceof Property) {
           $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
            if (!$entity instanceof Property) {
                return;
            }

            else if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
        }
    }

}