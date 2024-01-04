import React from "react";
import {getCurrentDate} from "../../utils";

let placeholderValue = "-";

export default function ExchangeRateRow({data, comparisonData}) {

    // Variant without any data to compare.
    if (!comparisonData) {
        return <tr>
            <td>{data.code || placeholderValue}</td>
            <td>{data.name || placeholderValue}</td>
            <td>{data.nbp || placeholderValue} </td>
            <td>{data?.sell || placeholderValue}</td>
            <td>{data?.buy || placeholderValue}</td>
        </tr>
    }

    // With comparison data.
    return <tr>
        <td>{comparisonData.code}</td>
        <td>{ data.name|| comparisonData.name || placeholderValue}</td>
        <td>
            <ValueWithComparison value={data.nbp} compareTo={comparisonData.nbp}/>
        </td>
        <td>
            <ValueWithComparison value={data.sell} compareTo={comparisonData.sell}/>
        </td>
        <td>
            <ValueWithComparison value={data.buy} compareTo={comparisonData.buy}/>
        </td>
    </tr>

}

function ValueWithComparison({value, compareTo}) {
    if (!value && !compareTo) {
        return <>{placeholderValue}</>
    }

    // color the compared values green if higher, red if lower
    const isHigher = value > compareTo;
    const isLower = value < compareTo;
    let cssClasses = '';
    if (isHigher) {
        cssClasses = 'text-danger';
    } else if (isLower) {
        cssClasses = 'text-success'
    }


    return <>
        {value || placeholderValue}&nbsp;
        (
        <abbr title={`Data for ${getCurrentDate()}`}>
            <span className={cssClasses}>
                {compareTo || placeholderValue}
            </span>
        </abbr>
        )
    </>
}