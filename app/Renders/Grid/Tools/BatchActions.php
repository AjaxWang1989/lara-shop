<?php

namespace App\Renders\Grid\Tools;

use App\Renders\SectionContent;
use Illuminate\Support\Collection;

class BatchActions extends AbstractTool
{
    /**
     * @var Collection
     */
    protected $actions;

    /**
     * @var bool
     */
    protected $enableDelete = true;

    /**
     * BatchActions constructor.
     */
    public function __construct()
    {
        $this->actions = new Collection();

        $this->appendDefaultAction();
    }

    /**
     * Append default action(batch delete action).
     *
     * return void
     */
    protected function appendDefaultAction()
    {
        $this->add('批量'.trans('admin.delete'), new BatchDelete());
    }

    /**
     * Disable delete.
     *
     * @return $this
     */
    public function disableDelete()
    {
        $this->enableDelete = false;

        return $this;
    }

    /**
     * Add a batch action.
     *
     * @param string      $title
     * @param BatchAction $abstract
     *
     * @return $this
     */
    public function add($title, BatchAction $abstract)
    {
        $id = $this->actions->count();

        $abstract->setId($id);

        $this->actions->push(compact('id', 'title', 'abstract'));

        return $this;
    }

    /**
     * Setup scripts of batch actions.
     *
     * @return void
     */
    protected function setUpScripts()
    {
        SectionContent::script($this->script());

        foreach ($this->actions as $action) {
            $abstract = $action['abstract'];
            $abstract->setResource($this->grid->resource());

            SectionContent::script($abstract->script());
        }
    }

    /**
     * Scripts of BatchActions button groups.
     *
     * @return string
     */
    protected function script()
    {
        return <<<'EOT'

$('.grid-select-all').iCheck({checkboxClass:'icheckbox_minimal-blue', "increaseArea":1}).css({
    position: "relative",
    top: 0,
    left: 0,
    display: "block",
    width: "auto",
    height: "auto",
    margin: "0px",
    padding: "0px",
    background: "rgb(255, 255, 255)",
    border: "0px",
    opacity: "0",
});

$('.grid-select-all').on('ifChanged', function(event) {
    if (this.checked) {
        $('.grid-row-checkbox').iCheck('check');
    } else {
        $('.grid-row-checkbox').iCheck('uncheck');
    }
});

var selectedRows = function () {
    var selected = [];
    $('.grid-row-checkbox:checked').each(function(){
        selected.push($(this).data('id'));
    });

    return selected;
}

EOT;
    }

    /**
     * Render BatchActions button groups.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->enableDelete) {
            $this->actions->shift();
        }

        if ($this->actions->isEmpty()) {
            return '';
        }

        $this->setUpScripts();

        return view('components.grid.batch-actions', ['actions' => $this->actions])->render();
    }
}
