<?php namespace Model;

use \Model\Artist;

class Album extends Model
{
//    protected static string $table = 'albums';
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
    protected int $id;
    private string $name;
    private string $genre;
    private int $trackAmount;
    private string $releaseDate;

    protected array $genres = [];
    protected ?int $artistId;
    protected ?Artist $artist;

    protected static array $constraints = [
        'artistId' => [
            'model' => '\Model\Artist',
            'property' => 'artist',
            'relationType' => 'oneToOne'
        ],
        'genres' => [
            'model' => '\Model\Genre',
            'relationType' => 'manyToMany',
            'conjunction' => [
                'table' => 'album_genre',
                'join' => 'albums.id = album_genre.album_id',
                [
                    'genres' => 'album_genre.genre_id = genres.id'
                ]
            ]
        ]
    ];

    public function __construct(
        int    $id,
        string $name,
        string $genre,
        int    $track_amount,
        string $release_date,
        ?int   $artist_id = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->genre = $genre;
        $this->trackAmount = $track_amount;
        $this->releaseDate = $release_date;
        $this->artistId = $artist_id;

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