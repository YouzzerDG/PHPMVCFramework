<?php namespace Model;

class Genre extends Model
{
    protected static array $table = [
        'name' => 'genres',
        'columns' => [
            'id',
            'name'
        ]
    ];

    protected int $id;
    private string $name;
    protected array $albums = [];

    protected static array $constraints = [
//        'albums' => [
//            'model' => '\Model\Album',
//            'relationType' => 'oneToMany',
//            'condition' => [
//                'join' => [
//                    'primary_key' => 'id',
//                    'foreign_key' => 'genre_id',
//                    'link_table' => [
//                        'album_genre',
//                        ['albums' => ['album_id', 'id']]
//                    ],
//                ],
//                'where' => [
//                    'statement' => 'album_genre.genre_id = :id'
//                ]
//            ],
//            'prepare' => [
//                ':id' => 'id'
//            ]
//        ]
        'albums' => [
            'model' => '\Model\Album',
            'relationType' => 'manyToMany',
            'conjunction' => [
                'table' => 'album_genre',
                'join' => 'genres.id = album_genre.genre_id',
                [
                    'albums' => 'album_genre.album_id = albums.id'
                ]
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
}