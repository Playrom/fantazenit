<?php

class User{
    private $id;
    private $balance;
    private $username;
    private $name;
    private $surname;
    private $password;
    private $email;
    private $reg_date;
    private $auth;
    private $players; // RosterList
    private $transfers;
    private $name_team;
    private $telephone;
    private $url_fb;
    private $apiKey;

    public function __construct($id=-1,$username,$name,$surname,$password,$email,$reg_date=NULL,$auth=0,$balance,$players=NULL,$transfers=NULL,$name_team,$telephone,$url_fb=NULL,$apiKey=NULL){
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
        //$arr['players'] = $this->players->map();
        //$arr['transfers'] = $this->transfers->map();
        $arr['name_team'] = $this->name_team;
        $arr['telephone'] = $this->telephone;
        $arr['url_fb'] = $this->url_fb;
        
        $arr['apiKey'] = $this->apiKey;
        
        return $arr;
        
    }

	

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the value of id.
     *
     * @param mixed $id the id 
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
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }
    
    /**
     * Sets the value of balance.
     *
     * @param mixed $balance the balance 
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
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Sets the value of username.
     *
     * @param mixed $username the username 
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param mixed $name the name 
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
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }
    
    /**
     * Sets the value of surname.
     *
     * @param mixed $surname the surname 
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
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Sets the value of password.
     *
     * @param mixed $password the password 
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
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Sets the value of email.
     *
     * @param mixed $email the email 
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
     * @return mixed
     */
    public function getRegDate()
    {
        return $this->reg_date;
    }
    
    /**
     * Sets the value of reg_date.
     *
     * @param mixed $reg_date the reg  date 
     *
     * @return self
     */
    public function setRegDate($reg_date)
    {
        $this->reg_date = $reg_date;

        return $this;
    }

    /**
     * Gets the value of auth.
     *
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }
    
    /**
     * Sets the value of auth.
     *
     * @param mixed $auth the auth 
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
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * Sets the value of players.
     *
     * @param mixed $players the players 
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
     * @return mixed
     */
    public function getTransfers()
    {
        return $this->transfers;
    }
    
    /**
     * Sets the value of transfers.
     *
     * @param mixed $transfers the transfers 
     *
     * @return self
     */
    public function setTransfers($transfers)
    {
        $this->transfers = $transfers;

        return $this;
    }

    public function getNameTeam(){
        return $this->name_team;
    }

    public function setNameTeam($name_team){
        $this->name_team=$name_team;
        return $this;
    }
    
    public function getTelephone(){
        return $this->telephone;
    }

    public function setTelephone($telephone){
        $this->telephone=$telephone;
        return $this;
    }
    
    public function getUrlFb(){
        return $this->url_fb;
    }

    public function setUrlFb($url_fb){
        $this->url_fb=$url_fb;
        return $this;
    }
    
    public function getApiKey(){
        return $this->apiKey;
    }
    
    public function setApiKey($apiKey){
        $this->apiKey=$apiKey;
        return $this;
    }
}