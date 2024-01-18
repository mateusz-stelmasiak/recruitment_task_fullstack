/**
 * @jest-environment jsdom
 */
import React from "react";
import {render, screen, test, expect, fireEvent, describe} from "../test-utils";
import "@testing-library/jest-dom";
import ExchangeRatesPage from "../../components/exchageRates/ExchageRatesPage";
import {getCurrentDate} from "../../utils";

const mockData = {
    data: {},
    loading: false,
    error: null
}
const mockChosenDateParam = {
    chosenDate: null,
};
jest.mock('../../hooks/useBackendAPI', () => jest.fn(() => {
    return mockData
}));
jest.mock('react-router-dom', () => ({
    ...jest.requireActual('react-router-dom'),
    useParams: () => (
        mockChosenDateParam
    ),
}));

describe('Date input', () => {

    test('is rendered on the page', async () => {
        render(<ExchangeRatesPage/>);
        const calendarInput = screen.getByLabelText('Choose a date');
        expect(calendarInput).toBeInTheDocument();
    })

    test('is set to today as default', () => {
        render(<ExchangeRatesPage/>);
        const calendarInput = screen.getByLabelText('Choose a date');
        const today = getCurrentDate();
        expect(calendarInput).toHaveAttribute('value', today);
    })

    test('changes on user input', () => {
        render(<ExchangeRatesPage/>);
        const calendarInput = screen.getByLabelText('Choose a date');
        const testDate = "2023-06-06";
        fireEvent.change(calendarInput, {target: {value: testDate}});
        expect(calendarInput).toHaveAttribute('value', testDate);
    })

    test('can be set by url params', () => {
        const testDate = "2023-07-06";
        mockChosenDateParam.chosenDate = testDate;

        render(<ExchangeRatesPage/>);
        const calendarInput = screen.getByLabelText('Choose a date');
        expect(calendarInput).toHaveAttribute('value', testDate);
    })


    test('changes if url params change',()=>{
        render(<ExchangeRatesPage/>);
        const calendarInput = screen.getByLabelText('Choose a date');
        const testDate = "2023-07-06";
        mockChosenDateParam.chosenDate = testDate;
        expect(calendarInput).toHaveAttribute('value',testDate);
    })
})

