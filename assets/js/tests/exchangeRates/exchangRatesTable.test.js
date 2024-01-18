/**
 * @jest-environment jsdom
 */
import React from "react";
import {render, screen, test, expect, fireEvent, describe} from "../test-utils";
import "@testing-library/jest-dom";
import ExchangeRatesTable from "../../components/exchageRates/ExchangeRatesTable";


const mockData = {
    data: {},
    loading: false,
    error: null
}
jest.mock('../../hooks/useBackendAPI', () => jest.fn(() => {
    return mockData
}));

describe('data loading and error display',()=>{

    test('shows loading animation while data is loading',()=>{
        mockData.loading = true;
        render(<ExchangeRatesTable/>);
        const loadingAnimation = screen.getByRole('status');
        expect(loadingAnimation).toBeInTheDocument();
    })

    test('shows no data available if no data was found',()=>{
        mockData.loading = false;
        mockData.data = {};
        render(<ExchangeRatesTable/>);
        const noDataAlert = screen.getByText(/.*No data available.*/i);
        expect(noDataAlert).toBeInTheDocument();
    })

    test('shows error message if error occured',()=>{
        const testError = "Some error"
        mockData.loading = false;
        mockData.data = {};
        mockData.error = testError;
        render(<ExchangeRatesTable/>);
        let regex = new RegExp(`.*${testError}.*`, "i");
        const errorAlert = screen.getByText(regex);
        expect(errorAlert).toBeInTheDocument();
    })
})