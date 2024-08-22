import axios from "axios";
import { useEffect, useState } from "react";




export default function useAnalyses() {
    const [analyses, setAnalyses] = useState([]);
    useEffect(() => {
        const fetchDataAnalyses = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/analyses');
                setAnalyses(response.data)
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }
        fetchDataAnalyses();
    }, []);
    return analyses;
}