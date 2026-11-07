<?php

namespace App\EventListener\Report;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class Report
{
    private bool $shouldInvalidate = false;

    public function __construct(private TagAwareCacheInterface $reportCache)
    {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();

        $entities = [
            ...$uow->getScheduledEntityInsertions(),
            ...$uow->getScheduledEntityUpdates(),
            ...$uow->getScheduledEntityDeletions()
        ];

        foreach($entities as $entity) {
            if($entity instanceof Book || $entity instanceof Author || $entity instanceof Subject){
                $this->shouldInvalidate = true;
                return;
            }
        }

        if($uow->getScheduledCollectionUpdates() !== []) {
            $this->shouldInvalidate = true;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if($this->shouldInvalidate) {
            $this->reportCache->invalidateTags(['report']);
            # TODO Verificar se precisa do export também, ou se o prefix está funcionando
            # $this->reportCache->invalidateTags(['report_export']);
            $this->shouldInvalidate = false;
        }
    }

}