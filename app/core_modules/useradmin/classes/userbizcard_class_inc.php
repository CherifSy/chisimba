<?php

/**
* User Business Card Generator
* 
* This class displays user details as a business class
* @author Tohir Solomons
*/
class userbizcard extends object
{

    /**
    * @var Array $userArray User Details stored as an array
    * @access private
    */
    private $userArray;
    
    /**
    * @var string $backgroundColor Background Color of the Business Card
    * @access public
    */
    public $backgroundColor='#EAEAEA';
    
    /**
    * @var boolean $showResetImage A flag on whether to show the reset button if user has a custom User Image
    * @access public
    */
    public $showResetImage=FALSE;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objCountries =& $this->getObject('countries', 'utilities');
    }
    
    /**
    * Method to pass the user details as an array to the class
    * @param array $userDetails
    */
    public function setUserArray($userDetails)
    {
        $this->userArray = $userDetails;
    }
    
    /**
    * Method to Display the Business Card
    * @return string
    */
    public function show()
    {
        switch ($this->userArray['sex'])
        {
            case 'M': $gender = 'Male'; break;
            case 'F': $gender = 'Female'; break;
            default : $gender = 'Unknown'; break;
        }

        $result = '<div style="width: 400px; background-color:'.$this->backgroundColor.'; height: 200px; border: 1px solid black;">
    <div class="floatlangdir" style="width:120px; height: 200px; background-color:white;">
        <div style="padding: 10px; text-align:center;">'.$this->objUser->getUserImage($this->userArray['userid'], TRUE);
        
        if ($this->showResetImage) {
            if ($this->objUser->hasCustomImage($this->userArray['userid'])) {
                $resetimageform = new form('updateimage', $this->uri(array('action'=>'resetimage'), 'userdetails'));
                $button = new button ('resetimage', 'Reset Image');
                $button->setToSubmit();
                $resetimageform->addToForm(' '.$button->show());
                $result .= $resetimageform->show();
            }
        }
        
        $result .= '</div>
    </div>
    <div class="floatlangdir" style="width: 280px">
        <div style="padding-left: 10px;padding-right: 10px;">
            <h1>'.$this->userArray['title'].' '.$this->userArray['firstname'].' '.$this->userArray['surname'].'</h1>
            <p style="line-height: 200%;"><strong>Email:</strong> '.$this->userArray['emailaddress'].'
            <br /><strong>Country:</strong> '.$this->objCountries->getCountryName($this->userArray['country']).' '.$this->objCountries->getCountryFlag($this->userArray['country']).'
            <br /><strong>Sex:</strong> '.$gender.'</p>
        </div>
    </div>
</div><br class="clearfloatlangdir" />';
        
        return $result;
    }

}

?>