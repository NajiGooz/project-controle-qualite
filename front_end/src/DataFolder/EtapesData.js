import axios from "axios";
import { useEffect, useState } from "react";




export default function useEtapes() {
    const [etapes, setEtapes] = useState([]);
    useEffect(() => {
        const fetchDataEtape = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/etapesProcess');
                setEtapes(response.data)
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }
        fetchDataEtape();
    }, []);
    return etapes;
}