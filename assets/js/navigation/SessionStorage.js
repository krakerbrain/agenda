export const SessionStorage = {
  getItem(key) {
    try {
      return sessionStorage.getItem(key);
    } catch (error) {
      console.error("SessionStorage read error:", error);
      return null;
    }
  },

  setItem(key, value) {
    try {
      sessionStorage.setItem(key, value);
      return true;
    } catch (error) {
      console.error("SessionStorage write error:", error);
      return false;
    }
  },

  removeItem(key) {
    try {
      sessionStorage.removeItem(key);
      return true;
    } catch (error) {
      console.error("SessionStorage remove error:", error);
      return false;
    }
  },
};
