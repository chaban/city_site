<?php namespace Models;


class Banner extends \Phalcon\Mvc\Model 
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $title;

    /**
     * @var string
     *
     */
    public $url;

    /**
     * @var string
     *
     */
    public $desc_r;

    /**
     * @var string
     *
     */
    public $desc_l;

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
     * Initializer method for model.
     */
    public function initialize()
    {        
        $this->belongsTo("update_user_id", "User", "id");
    }

}
