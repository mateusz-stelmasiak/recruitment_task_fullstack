//returns the current date in YYYY-MM-DD format
export const getCurrentDate = () => {
    const today = new Date();
    const year = today.getFullYear();
    let month=today.getMonth()+1;
    if(month<10) month ="0"+month;
    let day = today.getDate();
    if(day<10) day= "0"+day;

    return `${year}-${month}-${day}`;
}

