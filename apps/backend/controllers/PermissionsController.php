<?php
namespace Backend\Controllers;
use Phalcon\Tag as Tag;
use Models\Roles, Models\Permissions;

class PermissionsController extends ControllerBase
{
  
  public function initialize()
	{
		$this->view->setTemplateAfter('main');
		$this->view->setTemplateBefore('users');
		Tag::setTitle('Управление правами доступа ролей');
	}

	public function indexAction()
	{

		if ($this->request->isPost())
		{

			//Validate the role
			$role = Roles::findFirstById($this->request->getPost('role_id'));

			if ($role)
			{

				if ($this->request->hasPost('permissions'))
				{

					//Deletes the current permissions
					$role->getPermissions()->delete();

					//Save the new permissions
					foreach ($this->request->getPost('permissions') as $permission)
					{

						$parts = explode('.', $permission);

						$permission = new Permissions();
						$permission->rolesId = $role->id;
						$permission->resource = $parts[0];
						$permission->action = $parts[1];

						$permission->save();
					}

					$this->flash->success('Permissions were updated with success');
				}

				//Rebuild the ACL with
				$this->acl->rebuild();

				//Pass the current permissions to the view
				$this->view->permissions = $this->acl->getPermissions($role);

			}

			$this->view->role = $role;
		}

	}

}
