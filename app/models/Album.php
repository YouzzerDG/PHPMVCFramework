<?php namespace Model;

use \Model\Artist;

class Album extends Model
{
    protected static array $table = [
        'name' => 'albums',
        'columns' => [
            'id',
            'name',
            'genre',
            'track_amount',
            'release_date',
            'artist_id'
        ]
    ];

    protected array $genres = [];
    protected ?int $artistId;
    protected ?Artist $artist;

    protected static array $constraints = [
        'artistId' => [
            'model' => '\Model\Artist',
            'property' => 'artist',
            'relationType' => 'oneToOne'
        ],
        // 'genres' => [
        //     'model' => '\Model\Genre',
        //     'relationType' => 'manyToMany',
        //     'conjunction' => [
        //         'table' => 'album_genre',
        //         'join' => 'albums.id = album_genre.album_id',
        //         [
        //             'genres' => 'album_genre.genre_id = genres.id'
        //         ]
        //     ]
        // ]
    ];

    public function __construct(
        protected int    $id,
        private string $name,
        private string $genre,
        private int    $track_amount,
        private string $release_date,
        ?int   $artist_id = null
    )
    {
        parent::__construct($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getAmountOfTracks(): int
    {
        return $this->trackAmount;
    }

    public function getReleaseDate(): int
    {
        return $this->releaseDate;
    }

    public function getArtist(): Artist
    {
        return $this->artist;
    }
}