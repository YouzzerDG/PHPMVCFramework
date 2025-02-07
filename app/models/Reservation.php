<?php namespace Model;

class Reservation extends Model
{
    protected static array $table = [
        'name' => 'reservations',
        'columns' => [
            'id',
            'message',
            'start_date',
            'start_time',
            'end_time',
            'created_at'
        ],
        'foreign_keys' => [
            'service_id',
            'contact_id'
        ]
    ];

    protected static array $constraints = [
        'service' => [
            'model' => 'Model\Service',
            'relationType' => 'hasOne',
            'on' => [
                'primaryKey' => 'id',
                'foreignKey' => 'service_id'
            ]
        ],
        'contact' => [
            'model' => 'Model\Contact',
            'relationType' => 'hasOne',
            'on' => [
                'primaryKey' => 'id',
                'foreignKey' => 'contact_id'
            ]
        ]
    ];

    public Contact $contact;
    public Service $service;

    public function __construct(
        private int     $id,
        private string  $message,
        private ?string $start_date,
        private ?string $start_time,
        private ?string $end_time,
        private string  $created_at,
    )
    {

    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
            return $this->id;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
            return $this->message;
    }

    /**
     * Get the value of start_date
     */ 
    public function getStartDate()
    {
            return $this->start_date;
    }

    /**
     * Get the value of start_time
     */ 
    public function getStartTime()
    {
            return $this->start_time;
    }

    /**
     * Get the value of end_time
     */ 
    public function getEndTime()
    {
            return $this->end_time;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreatedAt()
    {
            return $this->created_at;
    }
}