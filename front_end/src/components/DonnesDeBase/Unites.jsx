import { useState, useEffect } from "react"
import axios from "axios"



export default function Unites() {

    const [unites, setUnites] = useState([])
    const [idUnite, setIdUnite] = useState('')
    useEffect(() => {
        const fetchUnites = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/unites`)
                if (response.data.length > 0) {
                    const lastUnite = response.data[response.data.length - 1]
                    setIdUnite(lastUnite.id + 1)
                } else {
                    setIdUnite(1)
                }
                setUnites(response.data)
            } catch (err) {
                console.error('Error fetching products:', err);
            }
        }
        fetchUnites()
    }, []);

    const [unite, setUnite] = useState({
        id: idUnite,
        codeUnite: '',
        libelleUnite: ''
    })
    useEffect(() => {
        if (idUnite !== undefined) {
            setUnite((prevUnite) => ({
                ...prevUnite,
                id: idUnite
            }))
        }
    }, [idUnite])
    const [updatedUnite, setUpdatedUnite] = useState({
        codeUnite: '',
        libelleUnite: ''
    })

    const [messageError, setMessageError] = useState('')

    useEffect(() => {
        if (messageError) {
            const timer = setTimeout(() => {
                setMessageError('');
            }, 10000);

            return () => clearTimeout(timer);
        }
    }, [messageError]);

    const [display, setDisplay] = useState('hidden');

    const handlChangeUnite = (e) => {
        const { name, value } = e.target;
        setUnite({
            ...unite, [name]: value
        })
    }
    const handlClickUnite = async (e) => {
        e.preventDefault();
        try {
            await axios.post(`http://127.0.0.1:8000/api/unite/`, unite)
            setUnites([
                ...unites, {
                    id: unite.id,
                    codeUnite: unite.codeUnite,
                    libelleUnite: unite.libelleUnite
                }
            ]
            );
            setUnite({
                codeUnite: "",
                libelleUnite: ""
            })
            setIdUnite(idUnite + 1);
        } catch (error) {
            console.log(error);
            setMessageError(error.response.data.message);
        }
    }


    const buttonModifier = (id) => {
        setDisplay('block');
        const unite = unites.find(unite => unite.id === id);
        setUpdatedUnite({
            id: unite.id,
            codeUnite: unite.codeUnite,
            libelleUnite: unite.libelleUnite,
        });
    }
    const handlChangeUpdatedUnite = (e) => {
        const { name, value } = e.target;
        setUpdatedUnite({
            ...updatedUnite, [name]: value
        })
        console.log(updatedUnite)
    }
    const handleUpdateUnite = async (e, id) => {
        e.preventDefault();
        try {
            await axios.put(`http://127.0.0.1:8000/api/unite/${id}`, updatedUnite)
            setUnites(unites.map(unite => unite.id === id ? updatedUnite : unite));
        } catch (error) {
            console.log(error);
            const errModification = "Integrity constraint violation: 1451 Cannot delete or update a parent row";
            if (error.response.data.message.includes(errModification)) {
                setMessageError("Cette Unite est liée à une Unite de Process ou un Parametre d'Unite et ne peut pas être modifier");
            } else {
                setMessageError(error.response.data.message);
            }
        }
        setDisplay('hidden');
    }

    const deleteUnite = async (id) => {
        try {
            const response = await axios.delete(`http://127.0.0.1:8000/api/unite/${id}`);
            setUnites(unites.filter(unite => unite.id !== id));
            setMessageError(response.message);
        } catch (error) {
            console.log(error);
            setMessageError(error.response.data.message);
        }
    }

    const [currentPage, setCurrentpage] = useState(1)
    const recordsPerPage = 5;
    const lastIndex = currentPage * recordsPerPage;
    const firstIndex = lastIndex - recordsPerPage;
    const records = unites.slice(firstIndex, lastIndex);
    const npage = Math.ceil(unites.length / recordsPerPage);
    const numbers = [...Array(npage + 1).keys()].slice(1)

    const prePage = (e) => {
        e.preventDefault()
        if (currentPage !== 1) {
            setCurrentpage(currentPage - 1)
        }
    }
    const changeCPage = (e, number) => {
        e.preventDefault()
        setCurrentpage(number)
    }
    const nextPage = (e) => {
        e.preventDefault()
        if (currentPage !== npage) {
            setCurrentpage(currentPage + 1)
        }
    }
    return <>
        <h1 className="text-4xl font-primaryBold dark:text-white">Contrôle de qualité</h1> <br />
        <div className="relative">
            <div className={`shadow-xl p-2 absolute w-[388px] h-[292px] bg-[#238899] rounded-xl ${display}`} style={{ transform: "translate(-50%, -50%)", left: "50%", top: "50%", zIndex: "999" }}>
                <svg onClick={() => setDisplay('hidden')} className="ml-auto w-[30px] h-[30px] cursor-pointer text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                <form className="w-full px-2" onSubmit={(e) => handleUpdateUnite(e, updatedUnite.id)}>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code Unite</label>
                        <input type="text" onChange={handlChangeUpdatedUnite} name="codeUnite" value={updatedUnite.codeUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_Unite" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Libelle Unite</label>
                        <input type="text" onChange={handlChangeUpdatedUnite} name="libelleUnite" value={updatedUnite.libelleUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Unite" required />
                    </div>
                    <div className="flex justify-center">
                        <button type="submit" className="font-primaryBold my-2 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Modifier</button>
                    </div>
                </form>
            </div>
            <p className="text-xl text-gray-900 dark:text-white" style={{ color: "#9A93B3" }}>Données de base / Unite</p> <br />
            <div className="container mx-auto px-20">
                {messageError &&
                    <div className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span className="font-medium">Error!</span> {messageError}
                    </div>
                }
                <form className="flex items-center justify-center gap-x-6" onSubmit={handlClickUnite}>
                    <div className="mb-5 w-96">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code Unite</label>
                        <input type="text" onChange={handlChangeUnite} name="codeUnite" value={unite.codeUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_Unite" required />
                    </div>
                    <div className="mb-5 w-96">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Libelle Unite</label>
                        <input type="text" onChange={handlChangeUnite} name="libelleUnite" value={unite.libelleUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Unite" required />
                    </div>
                    <button type="submit" className="font-primaryBold mt-3.5 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Crée</button>
                </form>

                <div className="shadow-xl relative overflow-x-auto rounded-lg">
                    <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Code Unite
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Libelle Unite
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                records.map((unite, key) => {
                                    return <tr key={key} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" className="px-6 py-4 font-primaryBold text-gray-900 whitespace-nowrap dark:text-white">
                                            {unite.codeUnite}
                                        </th>
                                        <td className="px-6 py-4 font-primaryMedium">
                                            {unite.libelleUnite}
                                        </td>
                                        <td className="px-6 py-4 flex justify-center gap-x-4">
                                            <button onClick={() => deleteUnite(unite.id)} className="font-primaryBold text-red-600">Supprimer</button>
                                            <button onClick={() => buttonModifier(unite.id)} className="font-primaryBold text-blue-600">modifier</button>
                                        </td>
                                    </tr>
                                })
                            }
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example" className="w-full">
                        <ul className="flex items-center justify-center -space-x-px h-8 text-sm">
                            <li>
                                <a href="#" onClick={prePage} className="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <span className="sr-only">Previous</span>
                                    <svg className="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 1 1 5l4 4" />
                                    </svg>
                                </a>
                            </li>
                            {
                                numbers.map((number, key) => (
                                    <li key={key}>
                                        <a href="#" onClick={(e) => changeCPage(e, number)} className={`flex items-center justify-center px-3 h-8 leading-tight border ${number == currentPage ? " font-primaryBold text-blue-600 border-blue-300 bg-blue-50" : "font-primaryRegular text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700"} dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white`}>{number}</a>
                                    </li>
                                ))
                            }
                            <li>
                                <a href="#" onClick={nextPage} className="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <span className="sr-only">Next</span>
                                    <svg className="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </>
}