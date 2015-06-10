<?php

/**
 * Class User
 */
class User{
    /**
     * @var int $id
     */
    private $id;
    /**
     * @var int $balance
     */
    private $balance;
    /**
     * @var string $username
     */
    private $username;
    /**
     * @var string $name
     */
    private $name;
    /**
     * @var string $surname
     */
    private $surname;
    /**
     * @var string $password
     */
    private $password;
    /**
     * @var string $email
     */
    private $email;
    /**
     * @var DateTime $reg_date
     */
    private $reg_date;
    /**
     * @var int $auth
     */
    private $auth;
    /**
     * @var RosterList $players
     */
    private $players; // RosterList
    /**
     * @var Transfer[] $transfers
     */
    private $transfers;
    /**
     * @var String $name_team
     */
    private $name_team;
    /**
     * @var String $telephone
     */
    private $telephone;
    /**
     * @var String $url_fb
     */
    private $url_fb;
    /**
     * @var String $apiKey
     */
    private $apiKey;


    /**
     * @param int $id
     * @param String $username
     * @param String $name
     * @param String $surname
     * @param String $password
     * @param String $email
     * @param DateTime $reg_date
     * @param int $auth
     * @param int $balance
     * @param RosterList $players
     * @param Transfer[] $transfers
     * @param String $name_team
     * @param String $telephone
     * @param String $url_fb
     * @param String $apiKey
     */
    public function __construct($id=-1,$username,$name,$surname,$password,$email,$reg_date=NULL,$auth=0,$balance,$players=NULL,$transfers=array(),$name_team,$telephone,$url_fb=NULL,$apiKey=NULL){
        $this->id=$id;
        $this->username=$username;
        $this->name=$name;
        $this->surname=$surname;
        $this->password=$password;
        $this->email=$email;
        $this->reg_date=$reg_date;
        $this->auth=$auth;
        $this->balance=$balance;
        $this->players=$players;
        $this->transfers=$transfers;
        $this->name_team=$name_team;
        $this->telephone=$telephone;
        $this->url_fb=$url_fb;
        
        $this->apiKey=$apiKey;
    }

    /**
     * @return mixed|mixed
     */
    public function map(){
        $arr=array();
        $arr['id']=$this->id;
        $arr['username']=$this->username;
        $arr['name']=$this->name;
        $arr['surname']=$this->surname;
        $arr['password']=  $this->password;
        $arr['email']=  $this->email;
        $arr['auth'] = $this->auth;
        $arr['balance'] = $this->balance;
        $arr['players'] = $this->players->map();

        $transferArray=array();

        foreach($this->transfers as $transfer){
            $transferArray[$transfer->getIdTransfer()]=$transfer->map();
        }



        $arr['transfers'] = $transferArray;
        $arr['name_team'] = $this->name_team;
        $arr['telephone'] = $this->telephone;
        $arr['url_fb'] = $this->url_fb;
        
        $arr['apiKey'] = $this->apiKey;



        return $arr;
        
    }

    /**
     * Map Only Basic Info
     *
     * @return String|mixed
     */

    public function mapBasic(){
        $arr=array();
        $arr['id']=$this->id;
        $arr['username']=$this->username;
        $arr['name']=$this->name;
        $arr['surname']=$this->surname;
        $arr['password']=  $this->password;
        $arr['email']=  $this->email;
        $arr['auth'] = $this->auth;
        $arr['balance'] = $this->balance;

        $arr['name_team'] = $this->name_team;
        $arr['telephone'] = $this->telephone;
        $arr['url_fb'] = $this->url_fb;

        $arr['apiKey'] = $this->apiKey;



        return $arr;

    }

	

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the value of id.
     *
     * @param int
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of balance.
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }
    
    /**
     * Sets the value of balance.
     *
     * @param int
     *
     * @return self
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Gets the value of username.
     *
     * @return String
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Sets the value of username.
     *
     * @param String
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param String
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of surname.
     *
     * @return String
     */
    public function getSurname()
    {
        return $this->surname;
    }
    
    /**
     * Sets the value of surname.
     *
     * @param String
     *
     * @return self
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Gets the value of password.
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Sets the value of password.
     *
     * @param String
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the value of email.
     *
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Sets the value of email.
     *
     * @param String
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of reg_date.
     *
     * @return DateTime
     */
    public function getRegDateTime()
    {
        return $this->reg_date;
    }
    
    /**
     * Sets the value of reg_date.
     *
     * @param DateTime
     *
     * @return self
     */
    public function setRegDateTime($reg_date)
    {
        $this->reg_date = $reg_date;

        return $this;
    }

    /**
     * Gets the value of auth.
     *
     * @return int
     */
    public function getAuth()
    {
        return $this->auth;
    }
    
    /**
     * Sets the value of auth.
     *
     * @param int
     *
     * @return self
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Gets the value of players.
     *
     * @return RosterList
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * Sets the value of players.
     *
     * @param RosterList
     *
     * @return self
     */
    public function setPlayers($players)
    {
        $this->players = $players;

        return $this;
    }

    /**
     * Gets the value of transfers.
     *
     * @return Transfer[]
     */
    public function getTransfers()
    {
        return $this->transfers;
    }
    
    /**
     * Sets the value of transfers.
     *
     * @param Transfer[]
     *
     * @return self
     */
    public function setTransfers($transfers)
    {
        $this->transfers = $transfers;

        return $this;
    }

    /**
     * @return String
     */
    public function getNameTeam(){
        return $this->name_team;
    }

    /**
     * @param String
     * @return $this
     */
    public function setNameTeam($name_team){
        $this->name_team=$name_team;
        return $this;
    }

    /**
     * @return String
     */
    public function getTelephone(){
        return $this->telephone;
    }

    /**
     * @param String
     * @return $this
     */
    public function setTelephone($telephone){
        $this->telephone=$telephone;
        return $this;
    }

    /**
     * @return String
     */
    public function getUrlFb(){
        return $this->url_fb;
    }

    /**
     * @param String
     * @return $this
     */
    public function setUrlFb($url_fb){
        $this->url_fb=$url_fb;
        return $this;
    }

    /**
     * @return String
     */
    public function getApiKey(){
        return $this->apiKey;
    }

    /**
     * @param String
     * @return $this
     */
    public function setApiKey($apiKey){
        $this->apiKey=$apiKey;
        return $this;
    }
}