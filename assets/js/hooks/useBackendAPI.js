import React, {useEffect, useMemo, useRef, useState} from "react";
import {getCurrentDate} from "../utils";
import axios from "axios";

export default function useBackendAPI(date) {
    const apiUrl = 'http://telemedi-zadanie.localhost/api';
    const currDate = getCurrentDate();
    const [data, setData] = useState({});
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    // Track the latest API call,
    // so that we can set loading to false only once that's done
    const latestCallId = useRef(null);

    let fetchData = (date, abortController) => {
        //check if the data has already been fetched
        if (data[date]) return;

        const callId = Date.now();
        latestCallId.current = callId;
        setLoading(true);
        setError(null);

        axios.get(`${apiUrl}/exchange-rates/${date}`,
            {signal: abortController.signal}
        ).then(response => {
            const fetchedData = response.data;
            if (!fetchedData || !Array.isArray(fetchedData)) {
                throw Error("Unexpected error.");
            }

            //transform array of objects into object with currency codes as keys
            const transformedData = fetchedData.reduce((acc, item) => {
                const key = Object.keys(item)[0];
                acc[key] = item[key];
                return acc;
            }, {});

            setData((data) => {
                return {
                    ...data,
                    [date]: transformedData
                }
            })
        }).catch(function (error) {
            //axios throws cancellation as an error, just ignore
            if (axios.isCancel(error)) { return;}

            //check if it's an error with a custom message
            const errorMsg = error.response?.data?.error || error;
            setError(errorMsg);

        }).finally(() => {
            // Only set to false if the last call is finished
            if (latestCallId.current !== callId) {
                return;
            }
            setLoading(false);
        });
    };

    //no matter the chosen date we'll have to fetch data for the current date
    useEffect(() => {
        const abortController = new AbortController();
        fetchData(currDate,abortController);

        return () => {
            abortController.abort();
        }
    }, [])

    //fetch data for the chosen date
    useEffect(() => {
        if (date === currDate) return;
        const abortController = new AbortController();
        fetchData(date,abortController);

        return () => {
            abortController.abort();
        }
    }, [date]);


    return {data, loading, error}
}