<?php
require_once './class/DAO.php';
require_once './class/ConverterConnection.php';

/**
 * CONTROLLER
 * The main class containing the logic of allowance calculations
 * Class AllowanceCalculator
 */
class AllowanceCalculator
{
    private string $price;
    private string $productName;
    private int $CNY_to_RUB;
    private int $finalPrice;
    private int $avgPrice;
    private int $allowance = 25;
    private int $finalAllowance;
    private int $priceWithAllow;
    private DAO $product;
    private array $valute;

    /**
     * Class instance constructor
     */
    public function __construct(array $data)
    {
        $this->valute = ConverterConnection::initArray();
        $this->product = new DAO();
        $this->price = $data['price'];
        $this->productName = $data['product'];
        $this->CNY_to_RUB = round((int)$data['price'] * (int)$this->valute['Valute']['CNY']['Value'] / (int)$this->valute['Valute']['CNY']['Nominal']);
        $this->finalPrice = $this->CNY_to_RUB + ($this->CNY_to_RUB * 0.65);
        $this->avgPrice = $this->product->getAvgPrice($this->productName);
        $this->finalAllowance = $this->allowanceCalc();
        $this->priceWithAllow = $this->priceWithAllowance();
    }

    /**
     * @return int
     * Allowance calculated
     */
    private function allowanceCalc(): int
    {
        $priceWithAllow = round($this->finalPrice + ($this->finalPrice * $this->allowance / 100));
        $finalAllowance = 0;
        $priceDifference = 100 - ($priceWithAllow * 100 / $this->avgPrice);
        if ($priceDifference > 5) {
            $finalAllowance = floor(($this->avgPrice - $this->finalPrice) * 100 / $this->finalPrice);
        } elseif ($priceDifference < 0) {
            $finalAllowance = round(100 - ($this->finalPrice / ($this->avgPrice / 100)) + 1);
        } else {
            $finalAllowance = $this->allowance;
        }

        return $finalAllowance;
    }

    /**
     * @return int
     * Price + allowance calculated
     */
    private function priceWithAllowance(): int
    {
        return round($this->finalPrice + ($this->finalPrice * $this->allowanceCalc() / 100));
    }

    /**
     * @return array
     * Return resulting array
     */
    public function getArray(): array
    {
        return ['price' => $this->price,
            'productName' => $this->productName,
            'finalPrice' => $this->finalPrice,
            'avgPrice' => $this->avgPrice,
            'finalAllowance' => $this->finalAllowance,
            'priceWithAllow' => $this->priceWithAllow,];
    }

    /**
     * Magic function __get()
     */
    /*    public function __get($name) {
            return match ($name) {
                'price' => $this->price,
                'productName' => $this->productName,
                'finalPrice' => $this->finalPrice,
                'avgPrice' => $this->avgPrice,
                'finalAllowance' => $this->finalAllowance,
                'priceWithAllow' => $this->priceWithAllow,
                default => 'The ' . $name . ' field does not exist',
            };
        }*/
}
