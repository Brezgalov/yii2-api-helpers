<?php

namespace Brezgalov\ApiHelpers\v2\DB;

use Brezgalov\ApiHelpers\v2\DB\DTO\IDTO;

class BaseUnitOfWork
{
    /**
     * @param IDTO $dto
     * @param IDataAccessHelper $dataHelper
     * @return array|false|int|mixed|string
     * @throws \Exception
     */
    public function storeDto(IDTO $dto, IDataAccessHelper $dataHelper)
    {
        if ($dto->isNew()) {
            $id = $dataHelper->insert($dto->toArray());

            if (!$id) {
                return false;
            }

            $dto->setId($id);
        } else {
            $success = $dataHelper->update($dto->getId(), $dto->toArray());

            if (!$success) {
                return false;
            }
        }

        return $dto->getId();
    }
}