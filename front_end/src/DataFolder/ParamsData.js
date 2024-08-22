import axios from "axios";
import { useEffect, useState } from "react";




export default function useParametres() {
    const [Params, setParams] = useState([]);
    useEffect(() => {
        const fetchDataParams = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/parametreAnalyses');
                setParams(response.data)
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }
        fetchDataParams();
    }, []);
    return Params;
}