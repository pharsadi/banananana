<?php namespace App\Repositories;

use App\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction implements RepositoryInterface
{
    /** @var Model  */
    protected $model;

    protected static $defaultPurchasePrice = 0.20;
    protected static $defaultSellPrice = 0.35;
    protected static $typePurchase = 'purchase';
    protected static $typeSell = 'sell';
    protected static $daysToExpire = 10;

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
     * @param $startDate
     * @param $endDate
     * @param Item $item
     * @return mixed
     */
    public function purchaseMetrics($startDate, $endDate, Item $item)
    {
        $metrics = $this->basicMetrics($startDate, $endDate, $item->name, static::$typePurchase);
        return $metrics;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param Item $item
     * @return mixed
     */
    public function sellMetrics($startDate, $endDate, Item $item)
    {
        $metrics = $this->basicMetrics($startDate, $endDate, $item->name, static::$typeSell);
        return $metrics;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $itemName
     * @param $transactionType
     * @return mixed
     */
    public function basicMetrics($startDate, $endDate, $itemName, $transactionType)
    {
        $metrics = $this->model->select(DB::raw('sum(quantity) as quantity, sum(quantity * price_per_item) as totalPrice'))
            ->where('item', $itemName)
            ->where('transaction_type', $transactionType)
            ->where('transaction_date', '<=', $endDate);

        if (!is_null($startDate)) {
            $metrics->where('transaction_date', '>=', $startDate);
        }

        return $metrics->first();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param Item $item
     * @return mixed
     */
    public function allInventory($startDate, $endDate, Item $item)
    {
        $purchaseMetrics = $this->purchaseMetrics($startDate, $endDate, $item);
        $sellMetrics = $this->sellMetrics($startDate, $endDate, $item);
        return ($purchaseMetrics->quantity - $sellMetrics->quantity);
    }

    /**
     * @param $date
     * @param $sellQuantity
     * @param Item $item
     * @return bool
     */
    public function canSell($date, $sellQuantity, Item $item)
    {
        $qualifiedDate = new \DateTime($date);
        $qualifiedDate = $qualifiedDate->modify('-' . static::$daysToExpire . ' days');

        $purchaseMetrics = $this->purchaseMetrics($qualifiedDate->format('Y-m-d'), $date, $item);
        $sellMetrics = $this->sellMetrics(NULL, $date, $item);

        return ($purchaseMetrics->quantity - $sellMetrics->quantity - $sellQuantity) > 0;
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

    /**
     * @param $startDate
     * @param $endDate
     * @param Item $item
     * @return array
     */
    public function metrics($startDate, $endDate, Item $item)
    {
        $purchaseMetrics = $this->purchaseMetrics($startDate, $endDate, $item);
        
        $expiredStartDate = new \DateTime($startDate);
        $expiredStartDate->modify('-' . static::$daysToExpire . ' days');
        $expiredEndDate = new \DateTime($endDate);
        $expiredEndDate->modify('-' . static::$daysToExpire . ' days');
        $expiredMetrics = $this->purchaseMetrics($expiredStartDate, $expiredEndDate, $item);

        $sellMetrics = $this->sellMetrics($startDate, $endDate, $item);

        return [
            'inventory' => $purchaseMetrics->quantity - $sellMetrics->quantity,
            'expiredInventory' => $expiredMetrics->quantity - $sellMetrics->quantity,
            'soldCount' => $sellMetrics->quantity,
            'profit' => $sellMetrics->totalPrice - $purchaseMetrics->totalPrice,
        ];
    }
}