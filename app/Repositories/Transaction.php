<?php namespace App\Repositories;

use App\Item;
use Illuminate\Database\Eloquent\Model;

class Transaction implements RepositoryInterface
{
    /** @var Model  */
    protected $model;

    protected static $defaultPurchasePrice = 0.20;
    protected static $defaultSellPrice = 0.35;
    protected static $typePurchase = 'purchase';
    protected static $typeSell = 'sell';

    /**
     * Constructor to bind model to repo
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all instances of model
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Creates a new record in the database
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record in the database
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $record = $this->find($id);
        return $record->update($data);
    }

    /**
     * Removes record from the database
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Show the record with the given id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get the associated model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }


    /**
     * Eager load database relationships
     *
     * @param $relations
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    /**
     * @param $data
     * @param $item
     * @return Model
     */
    public function purchase($data, Item $item)
    {
        $createData = [
            'item' => $item->name,
            'price_per_item' => $data['price'] ?? static::$defaultPurchasePrice,
            'transaction_type' => static::$typePurchase,
            'transaction_date' => $data['transaction_date'],
            'quantity' => $data['quantity']
        ];
        return $this->create($createData);
    }

    /**
     * @param $data
     * @param $item
     * @return Model
     */
    public function sell($data, Item $item)
    {
        $createData = [
            'item' => $item->name,
            'price_per_item' => $data['price'] ?? static::$defaultSellPrice,
            'transaction_type' => static::$typeSell,
            'transaction_date' => $data['transaction_date'],
            'quantity' => $data['quantity']
        ];
        return $this->create($createData);
    }
}