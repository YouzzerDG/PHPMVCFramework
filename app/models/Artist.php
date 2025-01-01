<?php namespace Model;

use \Model\Album;

class Artist extends Model
{
    //protected static string $table = 'artists';
    protected static array $table = [
        'name' => 'artists',
        'columns' => [
            'id',
            'name'
        ]
    ];

    protected int $id;
    private string $name;
    protected array $albums = [];

    protected static array $constraints = [
        'albums' => [
            'model' => '\Model\Album',
            'relationType' => 'manyToOne',
            'condition' => [
                'join' => [
                    'primary_key' => 'id',
                    'foreign_key' => 'artist_id'
                ],
                'where' => [
                    'statement' => 'albums.artist_id = :id'
                ]
            ],
            'prepare' => [
                ':id' => 'id'
            ]
        ]
    ];

    public function __construct(
        int    $id,
        string $name
    )
    {
        $this->id = $id;
        $this->name = $name;

        parent::__construct($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Album[]
     */
    public function getAlbums(): array
    {
        return $this->albums;
    }
}