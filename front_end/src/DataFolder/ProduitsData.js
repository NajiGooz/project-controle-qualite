import axios from "axios";
import { useEffect, useState } from "react";




export default function useProduit() {
    const [produits, setProduits] = useState([]);
    useEffect(() => {
        const fetchDataProduit = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/produits');
                setProduits(response.data)
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }
        fetchDataProduit();
    },[]);
    return produits;
}