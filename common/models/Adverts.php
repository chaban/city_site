<?php namespace Models;

class Adverts extends \Phalcon\Mvc\Model 
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var integer
     *
     */
    public $category;

    /**
     * @var string
     *
     */
    public $body;

    /**
     * @var integer
     *
     */
    public $status;

    /**
     * @var integer
     *
     */
    public $price_range;

    /**
     * @var string
     *
     */
    public $price;

    /**
     * @var integer
     *
     */
    public $buy_sell;

    /**
     * @var integer
     *
     */
    public $create_user_id;

    /**
     * @var integer
     *
     */
    public $update_user_id;

    /**
     * @var string
     *
     */
    public $create_time;

    /**
     * @var string
     *
     */
    public $update_time;


    /**
     * Initializer method for model.
     */
    public function initialize()
    {        
        $this->belongsTo("update_user_id", "User", "id");
    }

}
