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
        $id = $dto->getId();

        if ($dto->isNew()) {
            $dataHelper->update($id, $dto->toArray());
        } else {
            $id = $dataHelper->insert($dto->toArray());
            if ($id) {
                $dto->setId($id);
            }
        }

        return $id;
    }
}