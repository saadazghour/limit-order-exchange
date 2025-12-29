import axios from "axios"

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true, // This is important for Sanctum to work
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
})

// Interceptor to add the auth token to every request
apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem("authToken")
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  },
)

export default apiClient
