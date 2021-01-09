<?php

use Siam\ApiFilterPlugs\controller\ApiFilterController;

return [
    '/api/api-filter/add' => [['GET','POST'], [new ApiFilterController, 'add']],
    '/api/api-filter/get_list'  => [['GET','POST'], [new ApiFilterController, 'get_list']],
    '/api/api-filter/delete'   => [['GET','POST'], [new ApiFilterController, 'delete']],
    '/api/api-filter/edit'   => [['GET','POST'], [new ApiFilterController, 'edit']],
];