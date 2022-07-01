import { useNavigate, TitleBar, Loading } from '@shopify/app-bridge-react';
import { Card, EmptyState, Layout, Page, SkeletonBodyText } from '@shopify/polaris';
import { CustomerCard, ProductCard } from '../components';
import { useAppQuery } from '../hooks';


export default function HomePage() {
  /*
  Add an App Bridge useNavigate hook to set up the navigate function.
  This function modifies the top-level browser URL so that you can
  navigate within the embedded app and keep the browser in sync on reload.
  */
  const navigate = useNavigate()
  
  /*
    These are mock values. Setting these values lets you preview the loading markup and the empty state.
  */
    // const customerIsLoading = false;
    // const customerIsRefetching = true;
    // const Customers = ['yes'];
    // const customerMarkup = Customers?.length ? (
    //   <CustomerCard Customers={Customers} loading={customerIsRefetching} />
    // ): null;

    // useAppQuery({
    //   url: "api/products/update"
    // });

    // useAppQuery({
    //   url: "api/webhooks"
    // });

    // });
    // const Products = ['yes']
    // const productIsRefetching = false;
    // let productsMarkup = Products?.length ? (
    //   <ProductCard data={Products} loading={productIsRefetching} />
    // ): null;
  
    /* loadingMarkup uses the loading component from AppBridge and components from Polaris  */
  // const loadingMarkup = isLoading ? (
  //   <Card sectioned>
  //     <Loading />
  //     <SkeletonBodyText />
  //   </Card>
  // ) : null;

  /* Use Polaris Card and EmptyState components to define the contents of the empty state */
  const emptyStateMarkup =
    (false) ? (
    // !isProductLoading && (!Customers?.length || !Products?.length) ? (
    // !isLoading && !QRCodes?.length ? (
      <Card sectioned>
        <EmptyState
          heading="Get customer/product data"
          /* This button will update the customer and product data */
          action={{
            content: 'Get the data',
            onAction: () => {
              useAppQuery({
                url: "api/products/update"
              });
            },
          }}
          image="https://cdn.shopify.com/s/files/1/0262/4071/2726/files/emptystate-files.png"
        >
          <p>
            Display customer and product data.
          </p>
        </EmptyState>
      </Card>
    ) : null;
  /*
    Use Polaris Page and TitleBar components to create the page layout,
    and include the empty state contents set above.
  */
  return (
    <Page>
      <Layout>
        <Layout.Section>
          <ProductCard></ProductCard>
          <CustomerCard></CustomerCard>
          {/* {loadingMarkup} */}
          {/* {emptyStateMarkup}
          {customerMarkup} */}
          {/* {productsMarkup} */}
          {/* <ProductsCard/> */}
        </Layout.Section>
      </Layout>
    </Page>
  );
}
