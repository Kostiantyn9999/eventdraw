import React from "react";
import { QueryClient, QueryClientProvider } from "react-query"; // navigation
import AppRouter from "src/navigation/AppRouter";

import Toast from "./components/toast/Toast";
import { Container as ModalContainer } from "react-modal-promise";

const App = () => {
  const queryClient = new QueryClient({
    defaultOptions: {
      queries: {
        refetchOnWindowFocus: false,
      },
    },
  });

  return (
    <QueryClientProvider client={queryClient}>
      <>
        <ModalContainer />
        <AppRouter />
        <Toast />
      </>
    </QueryClientProvider>
  );
};

export default App;
