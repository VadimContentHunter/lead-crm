<?php

namespace crm\src\components\Security\_handlers;

use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use RuntimeException;

class HandleAccessRole
{
    public function __construct(
        private IAccessRoleRepository $roleRepository
    ) {
    }

    /**
     * Добавление новой роли.
     */
    public function addRole(string $name, ?string $description = null): AccessRole
    {
        $role = new AccessRole(name: $name, description: $description);
        $savedId = $this->roleRepository->save($role);

        if ($savedId <= 0) {
            throw new RuntimeException('Failed to save AccessRole');
        }

        $role->id = $savedId;
        return $role;
    }

    /**
     * Редактирование роли по ID — обновление без загрузки объекта.
     */
    public function editRoleById(int $roleId, ?string $newName = null, ?string $newDescription = null): bool
    {
        $data = ['id' => $roleId];
        if ($newName !== null) {
            $data['name'] = $newName;
        }
        if ($newDescription !== null) {
            $data['description'] = $newDescription;
        }

        return $this->roleRepository->update($data) !== null;
    }

    /**
     * Редактирование роли по имени — загружаем объект, так как по name нельзя обновлять напрямую.
     */
    public function editRoleByName(string $roleName, ?string $newName = null, ?string $newDescription = null): bool
    {
        $role = $this->roleRepository->getByName($roleName);
        if ($role === null) {
            return false;
        }

        $data = ['id' => $role->id];
        if ($newName !== null) {
            $data['name'] = $newName;
        }
        if ($newDescription !== null) {
            $data['description'] = $newDescription;
        }

        return $this->roleRepository->update($data) !== null;
    }

    public function deleteRole(int $roleId): bool
    {
        return $this->roleRepository->deleteById($roleId)  === null ? false : true;
    }

    public function getRoleById(int $roleId): ?AccessRole
    {
        return $this->roleRepository->getById($roleId);
    }

    public function getRoleByName(string $name): ?AccessRole
    {
        return $this->roleRepository->getByName($name);
    }
}
