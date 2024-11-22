<?php

namespace App\Http\View\Composers;

use App\Taxe;
use Illuminate\View\View;
use App\Http\Controllers\AjaxController;

class ViewComposer
{
    public $_ajax;
    
    public function __construct()
    {
        $this->_ajax = new AjaxController();
    }

    public function compose(View $view)
    {
        $view->with(['measures'=> $this->_ajax->getMeasures(), 'taxes' => Taxe::where('client_id', auth()->user()->headquarter->client_id)->get()]);
    }
}
