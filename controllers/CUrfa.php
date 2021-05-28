<?php

/**
 * Class CUrfa
 * Using:
 *      Web: //<IP>:14541/urfa
 *           api_key передавать в header или через GET
 *           param через POST
 *           Обязательные параметры : function, login, password
 */
class CUrfa extends Controller
{
    /**
     * CUrfa constructor.
     */
    function __construct()
    {
        parent::__construct(APIS);
        $_POST['system'] or Model::generateAnswer(400);
    }

    /**
     *
     */
    public function index()
    {
        $system = $_POST['system'];
        $host = $system['host'] ?? '';
        $function = $system['function'] ?? '';
        $version = $system['version'] ?? '';
        $login = $system['login'] ?? '';
        $password = $system['password'] ?? '';
        $byUser = $system['byUser'] ?? false;
        $data = $_POST['data'] ?? [];
        $urfaPath = $version ? '/netup/utm5-' . $version : '/netup/utm5';
        if (file_exists($urfaPath . '/xml/' . $this->app . '/' . $function . '.xml')) {
            if ($login && $password) {
                $this->model->urfaQuery($host, $urfaPath, $function, $login, $password, $data, $byUser);
            }
            else Model::generateAnswer(401, 'Unauthorized');
        }
        else Model::generateAnswer(404, 'Method is not defined');
    }

    /**
     * 200 - Успешно, если есть результат на вывод который нужно забрать
     * 204 - Любой успешный запрос (Удаление, добавление, изменение), где не требуется вывод результата
     * 400 - Если ошибка входных параметров.
     * 401 - Логин или пасс не верный
     * 404 - Если фукция не найдена
     */
}
