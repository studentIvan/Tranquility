<?php
class Admin
{
    protected static $checkCSRFToken = false;
    protected static $configuration = array();
    protected static $models = array();

    public static function control($matches)
    {
		/**
         * Loading configuration
         */
        self::$configuration = Process::$context['cms']['admin_cfg'];
	
        if (!in_array(Session::getRole(), self::$configuration['access_roles'])) {
            Process::$context['bot_secure'] = array(
                'input_login' => Security::getUniqueDigestForUserIP('input_login'),
                'input_pass' => Security::getUniqueDigestForUserIP('input_pass'),
                'input_check' => Security::getUniqueDigestForUserIP('input_check'),
            );

            list($login, $password, $remember) =
                Data::inputsList(
                    Process::$context['bot_secure']['input_login'],
                    Process::$context['bot_secure']['input_pass'],
                    Process::$context['bot_secure']['input_check']
                );

            if ($login and $password) {
                $temporary = $remember ? false : true;
                try {
                    Session::authorize($login, $password, $temporary);
                    if (!in_array(Session::getRole(), self::$configuration['access_roles'])) {
                        throw new AuthException('role error');
                    } else {
						afterSessionStartedCallback();
                        self::load($matches);
                    }
                } catch (Exception $e) {
                    Process::$context['flash_error'] = true;
                    Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
                }
            } else {
                Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
            }
        } else {
            self::load($matches);
        }
    }

    public static function load($matches)
    {
        $partition = isset($matches[1]) ?
            preg_replace('/[^a-z]/', '', $matches[1]) : false;
        $action = isset($matches[2]) ?
            preg_replace('/[^a-z0-9_]/', '', $matches[2]) : false;

        Process::$context['admin_part'] = $partition;
        Process::$context['admin_part_action'] = $action;

        $thisDir = dirname(__FILE__);

        /**
         * check CSRF Token (optionally)
         */
        self::$checkCSRFToken = (isset($_GET['csrf_token']) and
            Process::$context['csrf_token'] === $_GET['csrf_token']);

        /**
         * Fast action for logout
         */
        if ($partition === 'logout') {
            if (self::$checkCSRFToken) {
                Session::stop();
                Process::redirect(self::$configuration['base_uri']);
            } else {
                throw new ForbiddenException();
            }
        }

        /**
         * Init models
         */
        Process::$context['admin_menu_elements'] = array(array(
            'name' => 'Главная', 'uri' => '', 'icon' => 'icon-home',
        ));

        include_once $thisDir . '/../crud/interfaces/CRUDDriverInterface.php';
        include_once $thisDir . '/../crud/interfaces/CRUDObjectInterface.php';
        foreach (self::$configuration['registered_crud'] as $crud) {
            if (!class_exists($crud)) {
                $targetUser = $thisDir . '/../../../crud/' . $crud . '.php';
                $targetStandard = $thisDir . '/../crud/' . $crud . '.php';
                if (file_exists($targetUser)) {
                    include_once $targetUser;
                } elseif (file_exists($targetStandard)) {
                    include_once $targetStandard;
                } else {
                    throw new Exception("CRUD $crud not exists");
                }
            }

            /**
             * @var $p CRUDObjectInterface
             */
            $p = new $crud();

            if (($p instanceof CRUDObjectInterface) === false)
                throw new Exception("CRUD $p is not instance of CRUDObjectInterface");

            self::$models[] = $p;
			$menuInfo = $p->getInfo();
            if ($menuInfo !== false) 
				Process::$context['admin_menu_elements'][] = $menuInfo;
        }
		
		if ($partition === 'ajax') 
		{
            if (self::$checkCSRFToken and $selectedAction = Data::input('a')) 
			{
				/**
				 * @var $selectedPartitionModel CRUDObjectInterface
				 */
				$selectedPartitionModel = null;
				list($selectedElement, $selectedPartition) = Data::inputsList('e', 'p');
				
				foreach (self::$models as $m)
					if ($m->getMenuURI() === $selectedPartition) 
						$selectedPartitionModel = $m;
						
				if (is_null($selectedPartitionModel))
					throw new ForbiddenException();
				
                switch ($selectedAction) 
				{
                    case 'delete':
						try {
							$selectedPartitionModel->delete($selectedElement);
							echo 'ok';
						} catch(Exception $e) {
							Process::$context['flash_error'] = $e->getMessage();
							Process::getTwigInstance()->display('admin/alert.danger.html.twig', Process::$context);
						}
                        break;
						
					case 'edit-form':
						Process::$context['panel_base_uri'] = self::$configuration['base_uri'];
						Process::$context['admin_part'] = $selectedPartition;
						$returnPage = isset($_GET['rp']) ? preg_replace('/[^a-z0-9_]/', '', $_GET['rp']) : false;
						if ($returnPage) Process::$context['return_page'] = $returnPage;
						Process::$context['fields'] = $selectedPartitionModel->getFields();
						Process::$context['diff_field'] = $selectedPartitionModel->getDiffField();
						Process::$context['read_data'] = $selectedPartitionModel->readElement($selectedElement);
						Process::getTwigInstance()->display('admin/form.edit.html.twig', Process::$context);
                        break;
						
					case 'view':
						Process::$context['fields'] = $selectedPartitionModel->getFields();
						Process::$context['read_data'] = $selectedPartitionModel->readElement($selectedElement);
						Process::getTwigInstance()->display('admin/form.view.html.twig', Process::$context);
                        break;
						
					default:
						throw new NotFoundException();
                }
				
                exit;
            } 
			else 
			{
                throw new ForbiddenException();
            }
        }

		Process::$context['site_title'] = Process::$context['page_title'];
        Process::$context['page_title'] = 'Tranquility Admin';
        Process::$context['panel_base_uri'] = self::$configuration['base_uri'];
        if (!isset(Process::$context['current_user'])) {
            try {
                Process::$context['current_user'] = array(
                    'login' => Data::input(Process::$context['bot_secure']['input_login'])
                );
            } catch (Exception $e) {
                Process::$context['current_user'] = array(
                    'login' => 'Admin'
                );
            }
        }

        Process::$context['container'] = self::getContainer($partition, $action);
		$template = (Process::$context['container']['type'] == 'form') 
			? 'admin/form.html.twig' : 'admin/admin.html.twig';
        Process::getTwigInstance()->display($template, Process::$context);
    }

    public static function getContainer($partition, $action)
    {
        $container = array(
            'type' => 'page',
            'page' => 'homepage'
        );

        if ($partition)
        {
			if ($action == 'create' || $action == 'edit' || $action == 'view' || $action == 'delete') 
			{
				foreach (self::$models as $m)
				{
					/**
					 * @var $m CRUDObjectInterface
					 */
					if ($m->getMenuURI() === $partition)
					{
						$container['create_new_message'] = $m->getCreateString();
						if ($container['create_new_message'])
						{
							$container['type'] = 'form';
							$container['diff_field'] = $m->getDiffField();
							$container['fields'] = $m->getFields();
							$unique = isset($_GET['e']) ? $_GET['e'] : false;
							
							if ($action == 'view')
							{
								if (Data::input('post-action-edit') and self::$checkCSRFToken) 
								{
									$postedData = array();
									
									foreach(array_keys($container['fields']) as $fieldName) 
										$postedData[$fieldName] = Data::input("pf-$fieldName");
										
									try 
									{
										if (!$m->update($unique, $postedData))
											throw new Exception('edit error');
										if ($returnPage = Data::input('return-page'))
											Process::redirect($returnPage);
									}
									catch(Exception $e) 
									{
										Process::$context['exception_code'] = $e->getCode();
										Process::$context['flash_error'] = $e->getMessage();
									}
								}
							
								try {
									Process::$context['read_data'] = $m->readElement($unique);
									if (!Process::$context['read_data'])
										throw new NotFoundException();
									Process::$context['page_title'] = $m->getMenuName() . ' :: чтение';
								} catch (Exception $e) {
									throw new NotFoundException();
								}
							}
							elseif ($action == 'edit')
							{
								try {
									Process::$context['read_data'] = $m->readElement($unique);
									Process::$context['diff_field'] = $m->getDiffField();
									if (!Process::$context['read_data'])
										throw new NotFoundException();
									Process::$context['page_title'] = $m->getMenuName() . ' :: редактирование';
								} catch (Exception $e) {
									throw new NotFoundException();
								}
							}
							elseif ($action == 'delete')
							{
								if (!self::$checkCSRFToken)
									throw new ForbiddenException();
								try {
									$m->delete($unique);
									Process::redirect(Process::$context['panel_base_uri'] . '/' . $m->getMenuURI());
								} catch (Exception $e) {
									throw new NotFoundException();
								}
							}
							else
							{
								Process::$context['page_title'] = 
									$m->getMenuName() . ' :: ' . (($action == 'create') ? $container['create_new_message'] : 'изменить');
							}
						}
						else
						{
							throw new ForbiddenException();
						}
					}
				}
			} 
			else 
			{
				foreach (self::$models as $m)
				{
					/**
					 * @var $m CRUDObjectInterface
					 */
					if ($m->getMenuURI() === $partition)
					{
						$container['fields'] = $m->getFields();
						
						#region POST::CREATE
						if (Data::input('post-action-create') and self::$checkCSRFToken) {
							$postedData = array();
							foreach(array_keys($container['fields']) as $fieldName) {
								$postedData[$fieldName] = Data::input("pf-$fieldName");
							}
							try {
								if (!$m->create($postedData))
									throw new Exception('create error');
								foreach (Process::$context['admin_menu_elements'] as &$element) {
									if ($element['uri'] == $partition)
										$element['count']++;
								}
							} catch(Exception $e) {
								Process::$context['exception_code'] = $e->getCode();
								Process::$context['flash_error'] = $e->getMessage();
							}
						}
						#endregion
					
						if ($action and preg_match('/page_(\d+)/', $action, $matches)) {
							$page = $matches[1]*1;
						} else {
							$page = 1;
						}

						$count = $m->getCount();
						$perPage = $m->getElementsPerPageNum();
						$pagination = Data::paginate($count, $perPage, $page);
						Process::$context['page_title'] = $m->getMenuName() . ' :: управление';
						$container['type'] = 'listing';
						$container['count'] = ($count > $perPage) ? $perPage : $count;
						$container['all_count'] = $count;
						$container['diff_field'] = $m->getDiffField();
						$container['fields_displayable'] = $m->getDisplayable();
						$container['data'] = $m->getListing($pagination['offset'], $perPage);
						$container['create_new_message'] = $m->getCreateString();
						$container['only_display'] = $m->isOnlyDisplay();
						foreach ($container['fields'] as $f => $d) {
							if ($d['function']) {
								foreach ($container['data'] as &$e) {
									$function = $d['function'];
									if (function_exists($function)) {
										$e[$f] = $function($e[$f]);
									}
								}
							}
							if ($d['modify']) {
								foreach ($container['data'] as &$e) {
									$e[$f] = !empty($e[$f]) ? str_replace('$1', $e[$f], $d['modify']) : '';
								}
							}
						}
						Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
					}
				}
			}
        } else {
			try {
				/** Dashboard homepage */
				$totalSpace = round((Data::getDirSize(dirname(__FILE__) . '/../../../'))/1024/1024, 2);
				$freeSpace = Process::$context['hosting_free_space_mb'];
				Process::$context['total_space_info'] = $totalSpace . ' мб из ' . $freeSpace . ' мб';
				Process::$context['total_space_percent'] = ($totalSpace/$freeSpace)*100;
				Process::$context['visit_stats'] = Stats::getVisitors();
				Process::$context['users_stats'] = Stats::getUsers();
			} catch (Exception $e) {
				Process::$context['total_space_info'] = 'не удалось получить размер сайта';
			}
		}

        return $container;
    }
}
