<?php

namespace FGTCLB\OAuth2Server\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractRepository extends Repository {

    /** Create a query non respecting pid */
	public function createQuery(): QueryInterface
    {
		$query = parent::createQuery();
		$query->getQuerySettings()->setRespectStoragePage(false);

		return $query;
	}
}
