import React, {useState} from 'react';
import ExchangeRatesTable from "./ExchangeRatesTable";
import {useParams, useHistory} from "react-router-dom";
import {getCurrentDate} from "../../utils";

export default function ExchangeRatesPage() {
    const history = useHistory();
    const {chosenDate} = useParams(); //gets chosen date from url (if exists)
    const [selectedDate, setSelectedDate] = useState(chosenDate || getCurrentDate());

    let handleDateChange = (e) => {
        let selected = e.target.value;
        // Handle user clicking the "clear" button in datepicker UI
        if(!selected){
            selected = getCurrentDate();
        }
        setSelectedDate(selected);
        history.push(`/exchange-rates/${selected}`) //add to url
    }

    return (
        <div>
            <section className="row-section">
                <div className="container">
                    <div className="row mt-5">
                        <div className="col-md-12">
                            <div className="d-flex justify-content-between">
                                <div>
                                    <h1>Exchange rates</h1>
                                    <p className="fw-light">
                                        See the exchange rates for
                                        <span className={"font-weight-bold"}> {selectedDate} </span>
                                        below.
                                    </p>
                                </div>
                                <div className="d-flex flex-column align-items-end">
                                    <label htmlFor="datePicker">Choose a different date</label>
                                    <input type="date"
                                           id="datePicker"
                                           onChange={handleDateChange}
                                           value={selectedDate}
                                           min={"2023-01-01"}
                                           max={getCurrentDate()}
                                           aria-label="Choose date"
                                    />

                                </div>
                            </div>
                            <div className={"mt-3"}>
                                <ExchangeRatesTable date={selectedDate}/>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}