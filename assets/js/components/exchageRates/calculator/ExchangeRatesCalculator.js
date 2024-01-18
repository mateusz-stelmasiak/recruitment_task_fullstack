import React, {useEffect, useState} from 'react';
import CurrencySelector from "./CurrencySelector";

export default function ExchangeRatesCalculator({data}) {
    const [isSelling, setIsSelling] = useState(true);
    const [value, setValue] = useState(1);
    const [currencyFrom, setCurrencyFrom] = useState();
    const [currencyTo, setCurrencyTo] = useState();

    // If there is no data, no exchange rates can be calculated
    if (!data) return <></>;

    // Get available currencies from data object and filter ones
    // that have no exchange rate value
    let getAvailableFromCurrencies = () => {
        const currencies = Object.keys(data);
        if (isSelling) {
            return currencies.filter((currency) => {
                return data[currency]?.sell;
            });
        }
        return currencies.filter((currency) => {
            return data[currency]?.buy;
        });
    }
    let getAvailableToCurrencies = () => {
        const currencies = Object.keys(data);
        return currencies.filter((currency) => {
            return data[currency]?.nbp;
        });
    }

    const availableFromCurrencies = getAvailableFromCurrencies();
    const availableToCurrencies = getAvailableToCurrencies();
    // if there are no available currencies, no exchange rates can be calculated
    if ((!availableFromCurrencies || availableFromCurrencies.length === 0)
        || (!availableToCurrencies || availableToCurrencies.length === 0)
    ) {
        return <></>
    }

    let handleValueChange = (e) => {
        const newValue = e.target.value;
        setValue(newValue);
    }

    let handleIsSellingChange = (changeToSelling) => {
        setIsSelling(() => {
            return changeToSelling;
        })
    }

    let calculateExchange = () => {
        if (currencyFrom === currencyTo) return "-";

        const currencyFromRate = isSelling ? data[currencyFrom]?.sell : data[currencyFrom]?.buy
        const currencyToRate = data[currencyTo]?.nbp;
        if (!currencyFromRate || !currencyToRate) return "-";

        const exchangeRate = currencyFromRate / currencyToRate;
        return (value * exchangeRate).toFixed(2);
    }

    return (
        <>
            <h3>Exchange rates calculator</h3>

            {/*select buying or selling*/}
            <div className={"d-flex"}>
                <div className="form-check">
                    <input className="form-check-input"
                           type="radio"
                           name="selling"
                           id="selling"
                           onChange={() => handleIsSellingChange(true)}
                           checked={isSelling}/>
                    <label className="form-check-label" htmlFor="seling">
                        Selling
                    </label>
                </div>

                <div className="form-check ml-1">
                    <input className="form-check-input"
                           type="radio" name="buying"
                           id="buying"
                           checked={!isSelling}
                           onChange={() => handleIsSellingChange(false)}/>
                    <label className="form-check-label" htmlFor="buying">
                        Buying
                    </label>
                </div>
            </div>


            <div className={"d-flex mt-1"}>
                {/*select value and from currency*/}
                <div className={"d-flex flex-column col-md-2"}>
                    <input
                        min={0}
                        max={1000000}
                        value={value}
                        onChange={handleValueChange}
                        type={'number'}
                        className={"border-0"}
                    />
                    <CurrencySelector availableCurrencies={availableFromCurrencies}
                                      selected={currencyFrom}
                                      setSelected={setCurrencyFrom}
                    />
                </div>
                <span>=</span>
                {/*select to currency*/}
                <div className={"d-flex flex-column col-md-2"}>
                    <div className="card">
                        {calculateExchange()}
                    </div>
                    <CurrencySelector availableCurrencies={availableToCurrencies}
                                      selected={currencyTo}
                                      setSelected={setCurrencyTo}
                    />
                </div>

            </div>
        </>


    );
}