import axios from "axios";
import { useEffect, useState } from "react";




export default function useUnite() {
    const [unites, setUnites] = useState([]);
    useEffect(() => {
        const fetchDataUnite = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/unites');
                setUnites(response.data)
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }
        fetchDataUnite();
    },[]);
    return unites;
}