import React, {useEffect} from "react";


export default function CurrencySelector({availableCurrencies, setSelected, selected}) {

    // set first available as default
    useEffect(() => {
        if (!availableCurrencies) return;

        setSelected(availableCurrencies[0]);
    }, [])

    let handleChange = (e) => {
        setSelected(e.target.value);
    }

    return (
        <select
            value={selected}
            onChange={handleChange}
            className="form-select form-select-sm bg-dark text-white"
            aria-label=".form-select-sm example"
        >
            {availableCurrencies.map((currency) => {
                return <option key={currency}
                               value={currency}>
                            {currency}
                        </option>
            })}
        </select>
    );
}