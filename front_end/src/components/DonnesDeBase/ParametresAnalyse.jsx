import { useState, useEffect } from "react"
import axios from "axios"
import useUnite from "../../DataFolder/UnitesData";
import useAnalyses from "../../DataFolder/AnalysesData";



export default function ParametreAnalyses() {

    const [parametreAnalyses, setParametreAnalyses] = useState([])
    const [idParametreAnalyse, setIdParametreAnalyse] = useState('')
    useEffect(() => {
        const fetchParametreAnalyses = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/parametreAnalyses`)
                if (response.data.length > 0) {
                    const lastParametreAnalyse = response.data[response.data.length - 1]
                    setIdParametreAnalyse(lastParametreAnalyse.id + 1)
                } else {
                    setIdParametreAnalyse(1)
                }
                setParametreAnalyses(response.data)
            } catch (err) {
                console.error('Error fetching products:', err);
            }
        }
        fetchParametreAnalyses()
    }, []);
    const unites = useUnite();
    const analyses = useAnalyses();
    const [parametreAnalyse, setParametreAnalyse] = useState({
        id: idParametreAnalyse,
        codeParam: '',
        libelleParam: '',
        codeAnalyse: '',
        codeUnite: ''
    })
    useEffect(() => {
        if (idParametreAnalyse !== undefined) {
            setParametreAnalyse((prevParametreAnalyse) => ({
                ...prevParametreAnalyse,
                id: idParametreAnalyse
            }))
        }
    }, [idParametreAnalyse])
    const [updatedParametreAnalyse, setUpdatedParametreAnalyse] = useState({
        codeParam: '',
        libelleParam: '',
        codeAnalyse: '',
        codeUnite: ''
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

    const handlChangeParam = (e) => {
        const { name, value } = e.target;
        setParametreAnalyse({
            ...parametreAnalyse, [name]: value
        })
        console.log(name, value)
        console.log(parametreAnalyse)
    }
    const handlClickParam = async (e) => {
        e.preventDefault();
        try {
            await axios.post(`http://127.0.0.1:8000/api/parametreAnalyse/`, parametreAnalyse)
            setParametreAnalyses([
                ...parametreAnalyses, {
                    id: parametreAnalyse.id,
                    codeParam: parametreAnalyse.codeParam,
                    libelleParam: parametreAnalyse.libelleParam,
                    codeAnalyse: parametreAnalyse.codeAnalyse,
                    codeUnite: parametreAnalyse.codeUnite
                }
            ]
            );
            setParametreAnalyse({
                codeParam: "",
                libelleParam: "",
                codeAnalyse: "",
                codeUnite: ""
            })
            setIdParametreAnalyse(idParametreAnalyse + 1);
        } catch (error) {
            if (error.response && error.response.data) {
                console.log(error.response.data.message);
                setMessageError(error.response.data.message);
            } else {
                console.log(error.message);
                setMessageError('An unexpected error occurred.');
            }
        }
    }


    const buttonModifier = (id) => {
        setDisplay('block');
        const parametreAnalyse = parametreAnalyses.find(parametreAnalyse => parametreAnalyse.id === id);
        setUpdatedParametreAnalyse({
            id: parametreAnalyse.id,
            codeParam: parametreAnalyse.codeParam,
            libelleParam: parametreAnalyse.libelleParam,
            codeAnalyse: parametreAnalyse.codeAnalyse,
            codeUnite: parametreAnalyse.codeUnite
        });
    }
    const handlChangeUpdatedParam = (e) => {
        const { name, value } = e.target;
        setUpdatedParametreAnalyse({
            ...updatedParametreAnalyse, [name]: value
        })
        console.log(updatedParametreAnalyse)
    }
    const handleUpdateParam = async (e, id) => {
        e.preventDefault();
        try {
            await axios.put(`http://127.0.0.1:8000/api/parametreAnalyse/${id}`, updatedParametreAnalyse)
            setParametreAnalyses(parametreAnalyses.map(parametreAnalyse => parametreAnalyse.id === id ? updatedParametreAnalyse : parametreAnalyse));
        } catch (error) {
            console.log(error);
            const errModification = "Integrity constraint violation: 1451 Cannot delete or update a parent row";
            if (error.response.data.message.includes(errModification)) {
                setMessageError("Cette parametreAnalyse est liée à une parametreAnalyse de Process ou un Parametre d'parametreAnalyse et ne peut pas être modifier");
            } else {
                setMessageError(error.response.data.message);
            }
        }
        setDisplay('hidden');
    }

    const deleteParam = async (id) => {
        try {
            const response = await axios.delete(`http://127.0.0.1:8000/api/parametreAnalyse/${id}`);
            setParametreAnalyses(parametreAnalyses.filter(parametreAnalyse => parametreAnalyse.id !== id));
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
    const records = parametreAnalyses.slice(firstIndex, lastIndex);
    const npage = Math.ceil(parametreAnalyses.length / recordsPerPage);
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
            <div className={`shadow-xl p-2 absolute w-[388px] h-[492px] bg-[#238899] rounded-xl ${display}`} style={{ transform: "translate(-50%, -50%)", left: "50%", top: "50%", zIndex: "999" }}>
                <svg onClick={() => setDisplay('hidden')} className="ml-auto w-[30px] h-[30px] cursor-pointer text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                <form className="w-full px-2" onSubmit={(e) => handleUpdateParam(e, updatedParametreAnalyse.id)}>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code parametreAnalyse</label>
                        <input type="text" onChange={handlChangeUpdatedParam} name="codeParam" value={updatedParametreAnalyse.codeParam} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_parametreAnalyse" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Libelle parametreAnalyse</label>
                        <input type="text" onChange={handlChangeUpdatedParam} name="libelleParam" value={updatedParametreAnalyse.libelleParam} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_parametreAnalyse" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code parametreAnalyse</label>
                        <select onChange={handlChangeUpdatedParam} name="codeAnalyse" value={updatedParametreAnalyse.codeAnalyse} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="">Selectionnez une Analyse</option>
                            {analyses.map((analyse) => {
                                return <option key={analyse.id} value={analyse.codeAnalyse}>{analyse.libelleAnalyse}</option>
                            }
                            )}
                        </select>
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Libelle parametreAnalyse</label>
                        <select onChange={handlChangeUpdatedParam} name="codeUnite" value={updatedParametreAnalyse.codeUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="">Selectionnez une unite</option>
                            {unites.map((unite) => {
                                return <option key={unite.id} value={unite.codeUnite}>{unite.libelleUnite}</option>
                            }
                            )}
                        </select>
                    </div>
                    <div className="flex justify-center">
                        <button type="submit" className="font-primaryBold my-2 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Modifier</button>
                    </div>
                </form>
            </div>
            <p className="text-xl text-gray-900 dark:text-white" style={{ color: "#9A93B3" }}>Données de base / Parametre d&apos;Analyse</p> <br />
            <div className="container mx-auto px-20">
                {messageError &&
                    <div className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span className="font-medium">Error!</span> {messageError}
                    </div>
                }
                <form onSubmit={handlClickParam}>
                    <div className="flex items-end justify-center gap-x-6 mb-5">
                        <div>
                            <div className="flex items-center justify-center gap-x-6">
                                <div className="mb-5 w-96">
                                    <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code parametreAnalyse</label>
                                    <input type="text" onChange={handlChangeParam} name="codeParam" value={parametreAnalyse.codeParam} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_parametreAnalyse" required />
                                </div>
                                <div className="mb-5 w-96">
                                    <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Libelle parametreAnalyse</label>
                                    <input type="text" onChange={handlChangeParam} name="libelleParam" value={parametreAnalyse.libelleParam} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_parametreAnalyse" required />
                                </div>
                            </div>
                            <div className="flex items-center justify-center gap-x-6">
                                <div className="w-96">
                                    <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Analyse</label>
                                    <select onChange={handlChangeParam} name="codeAnalyse" value={parametreAnalyse.codeAnalyse} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        <option value="" disabled>Select Analyse</option>
                                        {
                                            analyses.map((analyse, key) => {
                                                return <option key={key} value={analyse.codeAnalyse}>{analyse.libelleAnalyse}</option>
                                            })
                                        }
                                    </select>
                                </div>
                                <div className="w-96">
                                    <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Unité de mesure</label>
                                    <select onChange={handlChangeParam} name="codeUnite" value={parametreAnalyse.codeUnite} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required >
                                        <option value="" disabled>Select Unite</option>
                                        {
                                            unites.map((unite, key) => {
                                                return <option key={key} value={unite.codeUnite}>{unite.libelleUnite}</option>
                                            })
                                        }

                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" className="font-primaryBold text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Crée</button>
                    </div>
                </form>
                <div className="shadow-xl relative overflow-x-auto rounded-lg">
                    <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Code_parametre
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Libelle_parametre
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Analyse
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Unite
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                records.map((parametreAnalyse, key) => {
                                    return <tr key={key} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" className="px-6 py-4 font-primaryBold text-gray-900 whitespace-nowrap dark:text-white">
                                            {parametreAnalyse.codeParam}
                                        </th>
                                        <td className="px-6 py-4 font-primaryMedium">
                                            {parametreAnalyse.libelleParam}
                                        </td>
                                        <td className="px-6 py-4 font-primaryMedium">
                                            {analyses
                                                .filter((analyse) => analyse.codeAnalyse === parametreAnalyse.codeAnalyse)
                                                .map((filteredAnalyse) => filteredAnalyse.libelleAnalyse)
                                            }
                                        </td>
                                        <td className="px-6 py-4 font-primaryMedium">
                                            {unites.filter(unite => unite.codeUnite === parametreAnalyse.codeUnite)
                                                .map(unite => unite.libelleUnite)}
                                        </td>
                                        <td className="px-6 py-4 flex justify-center gap-x-4">
                                            <button onClick={() => deleteParam(parametreAnalyse.id)} className="font-primaryBold text-red-600">Supprimer</button>
                                            <button onClick={() => buttonModifier(parametreAnalyse.id)} className="font-primaryBold text-blue-600">modifier</button>
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