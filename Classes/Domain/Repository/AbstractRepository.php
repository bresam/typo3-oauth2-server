<?php

namespace FGTCLB\OAuth2Server\Domain\Repository;;

use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractRepository extends Repository {
	/**
	 * Creates query and makes sure that no Extbase magic is performed with
	 * pid, languages, etc.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function createQuery() {
		$query = parent::createQuery();
		$query->getQuerySettings()->setRespectStoragePage(false);

		return $query;
	}
}
