<?php

namespace Oro\Bundle\SaleBundle\Entity\Repository;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SaleBundle\Entity\Quote;

/**
 * Doctrine repository for Quote entity.
 */
class QuoteRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    private function getQueryBuildertoFetchQuote(): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->select(['q', 'quoteProducts', 'quoteProductOffers'])
            ->leftJoin('q.quoteProducts', 'quoteProducts')
            ->leftJoin('quoteProducts.quoteProductOffers', 'quoteProductOffers');
    }

    /**
     * @param int $id
     * @return Quote|null
     */
    public function getQuote($id): ?Quote
    {
        $qb = $this->getQueryBuildertoFetchQuote();
        $qb->where($qb->expr()->eq('q.id', ':id'))
            ->setParameter('id', (int)$id);

        try {
            return $qb
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException|NonUniqueResultException $e) {
        }

        return null;
    }

    /**
     * @param string $guestAccessId
     * @return Quote|null
     */
    public function getQuoteByGuestAccessId(string $guestAccessId): ?Quote
    {
        $qb = $this->getQueryBuildertoFetchQuote();
        $qb->where($qb->expr()->eq('q.guestAccessId', ':guestAccessId'))
            ->setParameter('guestAccessId', $guestAccessId);

        try {
            return $qb
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException|NonUniqueResultException|DriverException $e) {
        }

        return null;
    }

    /**
     * @param array             $removingCurrencies
     * @param Organization|null $organization
     *
     * @return bool
     */
    public function hasRecordsWithRemovingCurrencies(
        array $removingCurrencies,
        Organization $organization = null
    ): bool {
        $qb = $this->createQueryBuilder('q');
        $qb
            ->select('COUNT(q.id)')
            ->leftJoin('q.quoteProducts', 'quoteProducts')
            ->leftJoin('quoteProducts.quoteProductOffers', 'quoteProductOffers')
            ->where($qb->expr()->in('quoteProductOffers.currency', ':removingCurrencies'))
            ->setParameter('removingCurrencies', $removingCurrencies);
        if ($organization instanceof Organization) {
            $qb->andWhere('q.organization = :organization');
            $qb->setParameter(':organization', $organization);
        }

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }
}
