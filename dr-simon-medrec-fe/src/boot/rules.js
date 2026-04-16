import { Notify } from "quasar";
import { boot } from "quasar/wrappers";

const rules = {
  required(val) {
    return val && val.length > 0 || "This field is required!";
  },
  requiredSelect() {
    return [(val) => val || "This field is required!"];
  },
  validateEmail(val) {
    const emailPattern =
      /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/;
    return emailPattern.test(val) || "Invalid email format";
  },
  confirmPassword(passwordToMatch) {
    return (val) => val === passwordToMatch || "Confirm password does not match!";
  },
  validateContact(val) {
    return /^09\d{9}$/.test(val) || "Invalid contact number format";
  },
  fileSizeRestrict(files) {
    const size = 3145728;
    return files.size < size || "Maximum of 3mb only";
  },
  fileTypeRestrict(files) {
    return files.type == "application/pdf" || "Accepts PDF file only";
  },
  imageRestriction(rejectedEntries) {
    Notify.create({
      type: "negative",
      message: `${rejectedEntries.length} Images does not pass on validation`,
    });
  },
};

export default boot(({ app }) => {
  app.config.globalProperties.$rules = rules
})

export { rules };
